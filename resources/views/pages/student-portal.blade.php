@extends('layouts.portal-panel')

@section('content')
{{-- Ultra-Premium Glassmorphic Hero Banner --}}
<section id="overview" class="relative rounded-3xl py-8 md:py-10 px-6 md:px-8 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border border-slate-800/80 overflow-hidden shadow-xl">
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute left-10 -bottom-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="space-y-6 relative z-10">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.portal')],
            ]
        ])

        {{-- Learner Header Info --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="relative">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-2xl sm:text-3xl flex items-center justify-center shadow-lg shadow-teal-500/20 border-2 border-teal-300/40">
                        {{ mb_substr(auth()->user()->name ?? 'S', 0, 1) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-2 border-slate-950 flex items-center justify-center text-[9px] font-bold">✓</span>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-block text-[11px] font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3 py-1 rounded-full border border-teal-700/60 shadow-xs">
                            {{ __('app.student_portal') }}
                        </span>
                        <span class="text-xs font-mono text-emerald-400 font-bold bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-800/60">
                            ● Active Enrollment
                        </span>
                    </div>
                    <h1 class="font-heading text-2xl sm:text-4xl font-black text-white tracking-tight">
                        {{ __('app.portal.welcome_back') }}، <span class="bg-gradient-to-r from-teal-300 to-emerald-400 bg-clip-text text-transparent underline decoration-orange-500 decoration-2 underline-offset-8">{{ auth()->user()->name ?? __('Learner') }}!</span>
                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm font-mono flex flex-wrap items-center gap-2 pt-1">
                        <span>🎓 {{ __('app.portal.grade_level') }}: <strong class="text-teal-300">{{ $studentProfile?->gradeLevel?->name ?: __('Grade 12 STEM') }}</strong></span>
                        <span class="text-slate-600">•</span>
                        <span>🏫 {{ __('app.portal.school') }}: <strong class="text-slate-200">{{ $studentProfile?->school_name ?: 'Elite STEM Academy Cairo' }}</strong></span>
                    </p>
                </div>
            </div>

            {{-- Quick Action Buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <button onclick="document.getElementById('excuseModal').classList.remove('hidden')" class="btn-lift px-5 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 text-xs font-extrabold rounded-2xl shadow-lg shadow-orange-500/20 cursor-pointer flex items-center gap-2 transition-all">
                    <span>📄</span> {{ __('app.portal.submit_excuse') }}
                </button>
                <button onclick="document.getElementById('homeworkExceptionModal').classList.remove('hidden')" class="btn-lift px-5 py-3 bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold rounded-2xl shadow-lg shadow-teal-600/20 cursor-pointer flex items-center gap-2 transition-all">
                    <span>📋</span> {{ __('app.portal.submit_exception') }}
                </button>
            </div>
        </div>
    </div>
</section>

<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 md:space-y-12">

        @if(! $hasActivePackage)
            <div class="animate-fade-in-up p-6 bg-rose-50/90 border border-rose-200 rounded-3xl flex flex-col sm:flex-row items-center justify-between gap-5 text-rose-950 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100/90 flex items-center justify-center text-rose-600 text-2xl font-bold shrink-0">💳</div>
                    <div class="space-y-1">
                        <h4 class="font-bold text-sm sm:text-base text-rose-950 leading-tight">{{ app()->getLocale() === 'ar' ? 'تنبيه: لا توجد باقة حصص نشطة لديك!' : 'Warning: No Active Package Subscription Found!' }}</h4>
                        <p class="text-xs font-mono text-rose-800 leading-relaxed">{{ app()->getLocale() === 'ar' ? 'يلزم الاشتراك في باقة حصص للتسجيل في الكورسات والدخول للبث المباشر والواجبات التفاعلية.' : 'An active package subscription with available session credits is required to enroll in courses, access live streams, and solve assignments.' }}</p>
                    </div>
                </div>
                <a href="{{ route('courses') }}" class="btn-lift px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-bold text-xs shadow-md shadow-rose-600/30 whitespace-nowrap flex items-center gap-2 shrink-0">
                    <span>🛒</span> {{ app()->getLocale() === 'ar' ? 'تصفح الكورسات والباقات الآن' : 'Browse Courses & Packages' }}
                </a>
            </div>
        @endif

        {{-- 4 Modern Stat Cards with Accent Borders --}}
        <div id="packages" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 md:mb-12 scroll-mt-28">
            {{-- Package & Remaining Sessions --}}
            <div class="animate-fade-in-up stagger-1 glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 border-t-4 {{ $hasActivePackage ? 'border-t-teal-500' : 'border-t-rose-500' }} shadow-sm hover:shadow-xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-extrabold {{ $hasActivePackage ? 'text-teal-800 bg-teal-50 border-teal-200/80' : 'text-rose-800 bg-rose-50 border-rose-200/80' }} px-3 py-1 rounded-full border shadow-2xs">{{ __('app.portal.current_package') }}</span>
                    <div class="w-11 h-11 rounded-2xl {{ $hasActivePackage ? 'bg-teal-50 text-teal-600 border border-teal-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }} flex items-center justify-center text-xl shadow-2xs">💳</div>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 leading-none pt-1">
                    @if($hasActivePackage)
                        {{ $package->remaining_sessions }} {{ app()->getLocale() === 'ar' ? 'حصص متبقية' : 'Sessions Remaining' }}
                    @else
                        <span class="text-rose-600 text-lg sm:text-xl font-bold">{{ app()->getLocale() === 'ar' ? 'لا توجد باقة نشطة (0 حصة)' : 'No Active Package (0 Credits)' }}</span>
                    @endif
                </p>
                <p class="text-xs font-mono text-slate-500 truncate pt-0.5">
                    @if($hasActivePackage)
                        {{ $package?->packageTemplate?->name ?: "Total: {$package->total_sessions} | Used: {$package->used_sessions}" }}
                    @else
                        <span class="text-rose-500 font-bold">{{ app()->getLocale() === 'ar' ? 'يلزم الاشتراك في باقة للوصول للكورسات والبث' : 'Subscription required to unlock courses & streams' }}</span>
                    @endif
                </p>
            </div>

            {{-- Upcoming Sessions --}}
            <div class="animate-fade-in-up stagger-2 glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 border-t-4 border-t-indigo-500 shadow-sm hover:shadow-xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-extrabold text-indigo-800 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200/80 shadow-2xs">{{ __('app.portal.upcoming_sessions') }}</span>
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-xl shadow-2xs">📅</div>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 leading-none pt-1">
                    {{ count($upcomingSessions) }} <span class="text-base font-bold text-slate-600">{{ app()->getLocale() === 'ar' ? 'حصص معتمدة' : 'Confirmed Sessions' }}</span>
                </p>
                <p class="text-xs font-mono text-slate-500 pt-0.5">{{ count($upcomingSessions) > 0 ? (app()->getLocale() === 'ar' ? 'مواعيد البث المباشر القادمة' : 'Upcoming Live Stream Schedule') : (app()->getLocale() === 'ar' ? 'لا توجد حصص قادمة حالياً' : 'No upcoming sessions scheduled') }}</p>
            </div>

            {{-- Attendance & Absence Rate --}}
            <div class="animate-fade-in-up stagger-3 glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 border-t-4 border-t-emerald-500 shadow-sm hover:shadow-xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-extrabold text-emerald-800 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/80 shadow-2xs">{{ __('app.portal.attendance_rate') }}</span>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-xl shadow-2xs">🎯</div>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 leading-none pt-1">
                    @if($totalSessionCount > 0)
                        {{ $attendanceRate }}% <span class="text-base font-bold text-slate-500">{{ $attendanceRate >= 80 ? (app()->getLocale() === 'ar' ? 'ممتاز' : 'Excellent') : (app()->getLocale() === 'ar' ? 'بحاجة للتحسين' : 'Needs Improvement') }}</span>
                    @else
                        <span class="text-lg text-slate-400 font-bold">{{ app()->getLocale() === 'ar' ? 'لا توجد حصص بعد' : 'No sessions yet' }}</span>
                    @endif
                </p>
                <p class="text-xs font-mono text-slate-500 pt-0.5">
                    {{ $attendedSessions }} {{ app()->getLocale() === 'ar' ? 'حصة حضور' : 'Attended' }} • {{ $approvedExcuses }} {{ app()->getLocale() === 'ar' ? 'أعذار مقبولة' : 'Approved Excuses' }}
                </p>
            </div>

            {{-- Homework Submissions Score --}}
            <div class="animate-fade-in-up stagger-4 glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 border-t-4 border-t-amber-500 shadow-sm hover:shadow-xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-extrabold text-amber-800 bg-amber-50 px-3 py-1 rounded-full border border-amber-200/80 shadow-2xs">{{ __('app.portal.homework_rate') }}</span>
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 text-xl shadow-2xs">📝</div>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 leading-none pt-1">
                    @if(!is_null($avgScore))
                        {{ $avgScore }}% <span class="text-base font-bold text-slate-500">{{ $avgScore >= 80 ? (app()->getLocale() === 'ar' ? 'ممتاز' : 'Excellent') : (app()->getLocale() === 'ar' ? 'مقبول' : 'Fair') }}</span>
                    @else
                        <span class="text-lg text-slate-400 font-bold">{{ app()->getLocale() === 'ar' ? 'لا توجد درجات بعد' : 'No grades yet' }}</span>
                    @endif
                </p>
                <p class="text-xs font-mono text-slate-500 pt-0.5">{{ count($submissions) }} {{ app()->getLocale() === 'ar' ? 'واجبات تم تصحيحها' : 'Submissions Evaluated' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            {{-- Main Dashboard Column --}}
            <div class="lg:col-span-8 space-y-8 lg:space-y-10">

                {{-- 1. Upcoming Live Sessions Section --}}
                <div id="liveSessions" class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-1 scroll-mt-28">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                                <span>📅</span> {{ __('app.portal.upcoming_sessions') }}
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'رابط الحصة التفاعلية يتفعل قبل موعد البث بـ 30 دقيقة بشرط تسليم الواجب أو طلب استثناء.' : 'Stream link activates 30 mins before start time provided homework or exception request is fulfilled.' }}</p>
                        </div>
                        <span class="text-xs font-mono font-bold bg-teal-50 text-teal-800 px-3.5 py-1.5 rounded-full border border-teal-200/80 self-start sm:self-auto shadow-2xs">
                            🛡️ 30-Min & Prerequisite Rules Active
                        </span>
                    </div>

                    <div id="upcomingSessionsContainer" class="space-y-4">
                        @if(! $hasActivePackage)
                            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl leading-none">⚠️</span>
                                    <div>
                                        <h4 class="font-bold text-sm text-amber-900">
                                            {{ app()->getLocale() === 'ar' ? 'تنبيه الحصص التجريبية والباقات:' : 'Demo & Package Subscription Alert:' }}
                                        </h4>
                                        <p class="text-xs font-mono text-amber-800 mt-0.5">
                                            {{ app()->getLocale() === 'ar' 
                                                ? 'الكورس لا يتضمن حصة تجريبية مجانية. يلزم الاشتراك في باقة حصص لتفعيل ودخول الحصص المباشرة والمنهجية.' 
                                                : 'Course does not have a free demo session. An active package subscription is required to unlock live streams and curriculum sessions.' }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('courses') }}" class="btn-lift px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold font-mono shadow-sm flex items-center gap-1.5 whitespace-nowrap self-stretch sm:self-auto justify-center">
                                    <span>🛒</span> {{ app()->getLocale() === 'ar' ? 'عرض الكورسات والتفعيل' : 'Explore Courses & Activate' }}
                                </a>
                            </div>
                        @endif

                        @forelse($upcomingSessions as $s)
                            @php
                                $state = $s->evaluateState(auth()->user());
                                $startAt = $s->effective_start_at;
                                $endAt = $s->effective_end_at;
                                $joinableAt = $s->joinable_at;
                            @endphp
                            <div class="p-5 bg-slate-50/80 hover:bg-slate-50 rounded-2xl border border-slate-200/90 space-y-4 transition-all hover:shadow-md hover:-translate-y-0.5">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5">
                                        @if($state === \App\Enums\LiveSessionState::LIVE)
                                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                                        @else
                                            <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                                        @endif
                                        <h3 class="font-bold text-base text-slate-900">{{ $s->title ?: (app()->getLocale() === 'ar' ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Session') }}</h3>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-mono font-bold">
                                        <span class="bg-blue-100 text-blue-900 px-3 py-1 rounded-full border border-blue-200 whitespace-nowrap">
                                            📅 {{ app()->getLocale() === 'ar' ? 'البداية' : 'Start' }}: {{ $startAt ? $startAt->format('Y-m-d h:i A') : 'Scheduled' }}
                                        </span>
                                        @if($startAt && $startAt->isFuture())
                                            <span class="session-countdown-pill bg-indigo-50 text-indigo-900 px-3 py-1 rounded-full border border-indigo-200 font-mono font-bold flex items-center justify-center gap-1.5 shadow-2xs whitespace-nowrap tabular-nums"
                                                  data-start-time="{{ $startAt->toIso8601String() }}"
                                                  data-join-time="{{ $joinableAt ? $joinableAt->toIso8601String() : $startAt->toIso8601String() }}">
                                                <span>⏳</span>
                                                <span class="countdown-text">{{ app()->getLocale() === 'ar' ? 'حساب الوقت...' : 'Calculating...' }}</span>
                                            </span>
                                        @endif
                                        @if($endAt)
                                            <span class="bg-slate-200/80 text-slate-800 px-3 py-1 rounded-full border border-slate-300/60 whitespace-nowrap">
                                                ⏱️ {{ app()->getLocale() === 'ar' ? 'النهاية' : 'End' }}: {{ $endAt->format('h:i A') }}
                                            </span>
                                        @endif
                                        @php
                                            $halfAt = $startAt ? $startAt->copy()->addMinutes((int) ceil(($s->duration_minutes ?: 60) / 2)) : null;
                                        @endphp
                                        @if($halfAt)
                                            <span class="bg-amber-100/90 text-amber-900 px-3 py-1 rounded-full border border-amber-300/80 whitespace-nowrap" title="{{ app()->getLocale() === 'ar' ? 'آخر موعد للدخول هو منتصف وقت الحصة' : 'Last allowed join time is half-session' }}">
                                                ⏳ {{ app()->getLocale() === 'ar' ? 'إغلاق الدخول' : 'Cutoff' }}: {{ $halfAt->format('h:i A') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-slate-200/70 text-xs font-mono text-slate-700">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <span>👨‍🏫 {{ app()->getLocale() === 'ar' ? 'المدرس' : 'Instructor' }}: <strong>{{ $s->teacherProfile?->user?->name ?: 'Dr. Instructor' }}</strong></span>
                                        <span>📚 {{ app()->getLocale() === 'ar' ? 'المادة' : 'Subject' }}: <strong>{{ $s->subject?->name ?: 'Physics' }}</strong></span>
                                    </div>

                                    @if($state === \App\Enums\LiveSessionState::LIVE)
                                        <a href="{{ route('student.meeting.show', ['id' => $s->id]) }}" class="btn-lift px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2">
                                            <span>🟢</span> {{ app()->getLocale() === 'ar' ? 'انضم للبث المباشر الان' : 'Join Live Stream' }}
                                        </a>
                                    @elseif($state === \App\Enums\LiveSessionState::BEFORE_JOINABLE)
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                            <span class="text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-300/80 px-4 py-2 rounded-xl flex items-center gap-2 shadow-2xs" title="{{ app()->getLocale() === 'ar' ? 'رابط الدخول ينشط تلقائياً قبل 30 دقيقة من موعد الحصة' : 'Join button activates 30 minutes before start time' }}">
                                                <span>🔒</span>
                                                <span>{{ app()->getLocale() === 'ar' ? 'يتفعل الدخول:' : 'Access Opens:' }}</span>
                                                <span class="text-teal-700 font-extrabold">{{ $joinableAt ? $joinableAt->format('h:i A') : ($startAt ? $startAt->format('h:i A') : '30 mins before') }}</span>
                                            </span>
                                        </div>
                                    @elseif($state === \App\Enums\LiveSessionState::PACKAGE_REQUIRED)
                                        <a href="{{ route('courses') }}" class="btn-lift text-xs font-mono font-extrabold bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200/90 px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all shadow-2xs">
                                            <span>🔒</span> {{ $state->label() }}
                                        </a>
                                    @elseif($state === \App\Enums\LiveSessionState::ENDED)
                                        <span class="text-xs font-mono font-bold bg-slate-100 text-slate-600 border border-slate-300 px-4 py-2 rounded-xl flex items-center gap-1.5">
                                            <span>⏹️</span> {{ $state->label() }}
                                        </span>
                                    @elseif($state === \App\Enums\LiveSessionState::PREREQUISITE_REQUIRED)
                                        <span class="text-xs font-mono font-bold bg-amber-100 text-amber-900 border border-amber-300 px-4 py-2 rounded-xl flex items-center gap-1.5">
                                            <span>⚠️</span> {{ $state->label() }}
                                        </span>
                                    @else
                                        <span class="text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-300/80 px-4 py-2 rounded-xl flex items-center gap-1.5">
                                            <span>🔒</span> {{ $state->label() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center text-slate-500 space-y-3 bg-slate-50/50 rounded-2xl border border-slate-200/80 p-6">
                                <div class="text-4xl">🎓</div>
                                <h3 class="font-bold text-slate-800 text-base">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد حصص مجانية متوفرة حالياً' : 'No Free Demo Sessions Currently Available' }}
                                </h3>
                                <p class="text-xs font-mono text-slate-600 max-w-md mx-auto">
                                    {{ app()->getLocale() === 'ar' 
                                        ? 'تم إغلاق الحصص التجريبية المجانية لهذه الكورسات. للانضمام للحصص المنهجية المباشرة والمتابعة الأكاديمية، يرجى تفعيل باقة حصص.' 
                                        : 'Free trial demo sessions for these courses are closed. Please subscribe to an active session package to join live streams.' }}
                                </p>
                                <div class="pt-2">
                                    <a href="{{ route('courses') }}" class="btn-lift inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold font-mono rounded-xl shadow-md shadow-indigo-600/20">
                                        <span>🚀</span> {{ app()->getLocale() === 'ar' ? 'استكشاف الكورسات والباقات المتاحة' : 'Explore Available Courses & Packages' }}
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 2. Pending & In-Progress MSQ Assignments Department --}}
                <div id="assignments" class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-2 scroll-mt-28">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                                <span>📝</span> {{ app()->getLocale() === 'ar' ? 'قسم الواجبات والاختبارات التفاعلية (Assignments & Quizzes)' : 'Assignments & MSQ Quizzes Department' }}
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'تظهر هنا الواجبات المتاحة والمستمرة لجميع الكورسات المشترك بها. بمجرد الإجابة تنتقل لسجل النتائج.' : 'Shows available and in-progress assignments for all your enrolled courses. Answered assignments move to submission history.' }}</p>
                        </div>
                        <span class="text-xs font-mono font-extrabold bg-teal-100 text-teal-900 px-3.5 py-1.5 rounded-full border border-teal-200 self-start sm:self-auto shadow-2xs">
                            {{ count($availableAssignments) }} {{ app()->getLocale() === 'ar' ? 'واجبات متاحة' : 'Available' }}
                        </span>
                    </div>

                    {{-- Course Filter Tabs for Assignments --}}
                    @if(isset($filterCourses) && count($filterCourses) > 1)
                        <div class="flex items-center gap-2 overflow-x-auto pb-1">
                            <button type="button" onclick="filterAssignmentsByCourse('all')" class="assign-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold font-mono transition-all bg-teal-600 text-white shadow-xs cursor-pointer" data-course="all">
                                {{ app()->getLocale() === 'ar' ? 'جميع الكورسات' : 'All Courses' }} ({{ count($availableAssignments) }})
                            </button>
                            @foreach($filterCourses as $fc)
                                @php
                                    $cCount = $availableAssignments->where('course_id', $fc->id)->count();
                                @endphp
                                <button type="button" onclick="filterAssignmentsByCourse({{ $fc->id }})" class="assign-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold font-mono transition-all bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer" data-course="{{ $fc->id }}">
                                    {{ $fc->title }} ({{ $cCount }})
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div id="availableAssignmentsContainer" class="space-y-4">
                        @forelse($availableAssignments as $assign)
                            @php
                                $isInProgress = isset($inProgressSubmissions[$assign->id]);
                                $courseTitle = $assign->course?->title ?: ($assign->liveSession?->course?->title ?: (app()->getLocale() === 'ar' ? 'كورس مادة التخصص' : 'Course Domain'));
                                $sessionTitle = $assign->session?->title ?: ($assign->liveSession?->title ?: (app()->getLocale() === 'ar' ? 'الجلسة التفاعلية' : 'Interactive Session'));
                            @endphp
                            <div class="available-assign-card p-6 bg-gradient-to-br from-teal-50/70 via-emerald-50/30 to-white rounded-3xl border border-teal-200/80 space-y-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all" data-course-id="{{ $assign->course_id ?? 0 }}">
                                
                                {{-- Course & Session Context Badges --}}
                                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-teal-100/80 pb-3 text-xs font-mono">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-teal-900 bg-teal-100/90 px-3 py-0.5 rounded-full border border-teal-200">
                                            📚 {{ $courseTitle }}
                                        </span>
                                        <span class="font-bold text-slate-800 bg-slate-200/80 px-3 py-0.5 rounded-full">
                                            📺 {{ $sessionTitle }}
                                        </span>
                                        @if($isInProgress)
                                            <span class="bg-amber-500 text-white px-2.5 py-0.5 rounded-full font-bold text-[10px] animate-pulse">
                                                ⚡ {{ app()->getLocale() === 'ar' ? 'قيد الحل حالياً' : 'In Progress' }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-teal-700 font-bold bg-teal-100/60 px-2.5 py-0.5 rounded-md">
                                        ⏱️ {{ $assign->duration_minutes ?: 30 }} {{ app()->getLocale() === 'ar' ? 'دقيقة إجابة' : 'Mins Duration' }}
                                    </span>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-mono font-bold uppercase {{ $isInProgress ? 'bg-amber-600' : 'bg-teal-700' }} text-white px-2 py-0.5 rounded">
                                                {{ $isInProgress ? 'Active Attempt' : 'MSQ Evaluation' }}
                                            </span>
                                            <h3 class="font-bold text-base text-slate-900">{{ $assign->title }}</h3>
                                        </div>
                                        <p class="text-xs text-slate-600 font-mono leading-relaxed">{{ $assign->description ?: (app()->getLocale() === 'ar' ? 'واجب تقييمي تفاعلي لغلق فجوات الدرس والتأكد من الفهم الكامل.' : 'Interactive MSQ assignment to verify lesson mastery.') }}</p>
                                    </div>
                                    <span class="text-xs font-mono font-extrabold text-slate-800 bg-white px-3.5 py-1.5 rounded-xl border border-slate-200 shadow-2xs self-start sm:self-auto">
                                        🎯 {{ app()->getLocale() === 'ar' ? 'درجة النجاح:' : 'Pass Mark:' }} {{ number_format($assign->passing_score ?? 70, 0) }}%
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 text-xs font-mono">
                                    <div class="flex flex-wrap items-center gap-3 text-slate-600">
                                        <span class="flex items-center gap-1 font-bold text-amber-900 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                                            ⏰ {{ app()->getLocale() === 'ar' ? 'الموعد النهائي' : 'Deadline' }}: 
                                            {{ $assign->effective_due_at ? $assign->effective_due_at->format('Y-m-d H:i') : '24h Pre-Session' }}
                                        </span>
                                        <span class="bg-slate-100 text-slate-800 px-3 py-1 rounded-full border border-slate-200 font-bold">
                                            🔒 {{ app()->getLocale() === 'ar' ? 'محاولة واحدة فقط' : '1 Attempt Only' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($isInProgress)
                                            <a href="{{ route('student.assignment.take', ['id' => $assign->id]) }}" class="btn-lift px-6 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl font-extrabold text-xs shadow-md shadow-amber-500/30 flex items-center gap-2">
                                                <span>⚡</span> {{ app()->getLocale() === 'ar' ? 'استكمال حل الواجب' : 'Resume Assignment' }} &rarr;
                                            </a>
                                        @else
                                            <a href="{{ route('student.assignment.take', ['id' => $assign->id]) }}" class="btn-lift px-6 py-3 bg-[#0D9488] hover:bg-[#0F766E] text-white rounded-xl font-extrabold text-xs shadow-md shadow-teal-600/30 flex items-center gap-2">
                                                <span>⚡</span> {{ app()->getLocale() === 'ar' ? 'بدء حل الواجب التفاعلي' : 'Start Interactive MSQ' }}
                                            </a>
                                        @endif
                                        <button onclick="openMsqAssignmentModal({{ $assign->id }})" class="btn-lift px-4 py-3 bg-white hover:bg-slate-100 text-slate-800 rounded-xl font-bold text-xs border border-slate-300 shadow-2xs cursor-pointer">
                                            {{ app()->getLocale() === 'ar' ? 'معاينة سريعة 👁️' : 'Quick Preview 👁️' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 bg-emerald-50/70 rounded-3xl border border-emerald-200 text-center space-y-2">
                                <div class="text-4xl animate-bounce">🎉</div>
                                <h4 class="font-bold text-lg text-emerald-950">{{ app()->getLocale() === 'ar' ? 'ممتاز! تم حل جميع الواجبات المتاحة بنجاح' : 'All Available Assignments Completed!' }}</h4>
                                <p class="text-xs font-mono text-emerald-800">{{ app()->getLocale() === 'ar' ? 'لا توجد واجبات معلقة حالياً. يمكن مراجعة النتائج والتفاصيل في قسم السجل أدناه.' : 'No pending assignments remaining. Inspect your scores and evaluations in the submission history below.' }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 2.5 Enrolled Courses & Detailed Sessions Roadmap --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                                <span>📚</span> {{ app()->getLocale() === 'ar' ? 'الكورسات المشترك بها والمنهج التفصيلي' : 'My Enrolled Courses & Detailed Modules' }}
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1">
                                {{ app()->getLocale() === 'ar' 
                                    ? 'استكشف المقررات الدراسية المشترك بها، وتصفح تفاصيل كل كورس، الجدول الزمني، والحصص المباشرة والمسجلة.' 
                                    : 'Explore your active enrolled courses, inspect full module roadmaps, live stream schedules, and session materials.' }}
                            </p>
                        </div>
                        <span class="text-xs font-mono font-extrabold bg-teal-100 text-teal-900 px-3.5 py-1.5 rounded-full border border-teal-200 self-start sm:self-auto shadow-2xs">
                            {{ count($enrollments) }} {{ app()->getLocale() === 'ar' ? 'كورسات مسجلة' : 'Active Courses' }}
                        </span>
                    </div>

                    @if(count($enrollmentCards) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($enrollmentCards as $card)
                                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group space-y-4">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="bg-teal-600 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-xs">
                                                {{ $card['subject'] }}
                                            </span>
                                            <span class="text-[11px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                                ✓ {{ app()->getLocale() === 'ar' ? 'مشترك بنجاح' : 'Enrolled' }}
                                            </span>
                                        </div>

                                        <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 group-hover:text-teal-600 transition-colors leading-tight">
                                            {{ $card['course']->title }}
                                        </h3>

                                        <p class="text-xs text-slate-600 font-mono leading-relaxed line-clamp-2">
                                            {{ $card['course']->description ?: (app()->getLocale() === 'ar' ? 'مقرر تعليمي تفاعلي شامل للمرحلة الثانوية مع متابعة واختبارات.' : 'Comprehensive interactive curriculum with practical labs.') }}
                                        </p>

                                        <div class="flex items-center gap-2 pt-1 text-xs font-mono text-slate-700">
                                            <img src="{{ asset('images/instructor_portrait.webp') }}" alt="{{ $card['teacher'] }}" class="w-6 h-6 rounded-full object-cover border border-teal-500">
                                            <span>👨‍🏫 <strong>{{ $card['teacher'] }}</strong></span>
                                        </div>

                                        {{-- Progress Bar --}}
                                        <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                            <div class="flex justify-between text-[11px] font-mono font-bold text-slate-600">
                                                <span>{{ app()->getLocale() === 'ar' ? 'نسبة إنجاز المنهج' : 'Curriculum Progress' }}</span>
                                                <span class="text-teal-600 font-extrabold">{{ $card['progressPct'] }}%</span>
                                            </div>
                                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ max(8, $card['progressPct']) }}%;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 text-xs font-mono text-slate-500">
                                            <span>📹 <strong>{{ $card['recCount'] }}</strong> {{ app()->getLocale() === 'ar' ? 'دروس' : 'Lectures' }}</span>
                                            <span>•</span>
                                            <span>🟢 <strong>{{ $card['liveCount'] }}</strong> {{ app()->getLocale() === 'ar' ? 'بث مباشر' : 'Live Streams' }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-2">
                                            <button onclick="openEnrolledCourseModal({{ $card['course']->id }})" class="btn-lift px-4 py-2 bg-slate-900 hover:bg-teal-600 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-1.5 cursor-pointer transition-all">
                                                <span>🔍</span> {{ app()->getLocale() === 'ar' ? 'التفاصيل والحصص' : 'Full Details & Sessions' }}
                                            </button>
                                            <a href="{{ route('course-details', ['slug' => $card['course']->slug]) }}" class="btn-lift px-3 py-2 bg-teal-50 hover:bg-teal-100 text-teal-800 rounded-xl text-xs font-bold border border-teal-200/80 flex items-center gap-1" title="{{ app()->getLocale() === 'ar' ? 'صفحة الكورس' : 'Course Page' }}">
                                                <span>▶</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center text-slate-500 space-y-3 bg-slate-50/50 rounded-2xl border border-slate-200/80 p-6">
                            <div class="text-4xl">📚</div>
                            <h3 class="font-bold text-slate-800 text-base">
                                {{ app()->getLocale() === 'ar' ? 'لم تقم بالتسجيل في أي كورس بعد' : 'No Enrolled Courses Yet' }}
                            </h3>
                            <p class="text-xs font-mono text-slate-600 max-w-md mx-auto">
                                {{ app()->getLocale() === 'ar' 
                                    ? 'تصفح قائمة الكورسات والمناهج المتاحة في الأكاديمية وقم بالتسجيل فوراً لبدء رحلة التعلم.' 
                                    : 'Explore the full course catalog and enroll in accredited STEM programs to unlock your modules.' }}
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('courses') }}" class="btn-lift inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold font-mono rounded-xl shadow-md shadow-teal-600/20">
                                    <span>🚀</span> {{ app()->getLocale() === 'ar' ? 'تصفح الكورسات المتاحة الآن' : 'Browse Available Courses' }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 3. Dedicated Assignment Submission History & Graded Evaluation Section --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                                <span>📜</span> {{ app()->getLocale() === 'ar' ? 'سجل تسليمات الواجبات والدرجات (Submissions History)' : 'Assignment Submission History & Graded Evaluation' }}
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'تظهر هنا جميع الواجبات التي تمت إجابتها لجميع الكورسات مع تفاصيل الكورس والجلسة والنتيجة المحققة.' : 'Complete record of all answered assignments across all your enrolled courses with evaluated scores.' }}</p>
                        </div>
                        <span class="text-xs font-mono font-extrabold bg-slate-100 text-slate-800 px-3.5 py-1.5 rounded-full border border-slate-200 self-start sm:self-auto shadow-2xs">
                            {{ count($submissions) }} {{ app()->getLocale() === 'ar' ? 'تسليمات سابقة' : 'Submitted' }}
                        </span>
                    </div>

                    {{-- Course Filter Tabs for Submissions --}}
                    @if(isset($filterCourses) && count($filterCourses) > 1)
                        <div class="flex items-center gap-2 overflow-x-auto pb-1">
                            <button type="button" onclick="filterSubmissionsByCourse('all')" class="sub-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold font-mono transition-all bg-teal-600 text-white shadow-xs cursor-pointer" data-course="all">
                                {{ app()->getLocale() === 'ar' ? 'جميع الكورسات' : 'All Courses' }} ({{ count($submissions) }})
                            </button>
                            @foreach($filterCourses as $fc)
                                @php
                                    $sCount = $submissions->filter(fn($s) => ($s->assignment?->course_id == $fc->id || $s->assignment?->liveSession?->course_id == $fc->id))->count();
                                @endphp
                                <button type="button" onclick="filterSubmissionsByCourse({{ $fc->id }})" class="sub-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold font-mono transition-all bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer" data-course="{{ $fc->id }}">
                                    {{ $fc->title }} ({{ $sCount }})
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div id="submissionsContainer" class="space-y-4">
                        @forelse($submissions as $sub)
                            @php
                                $subCourseId = $sub->assignment?->course_id ?: ($sub->assignment?->liveSession?->course_id ?: 0);
                                $subCourseTitle = $sub->assignment?->course?->title ?: ($sub->assignment?->liveSession?->course?->title ?: (app()->getLocale() === 'ar' ? 'كورس مادة التخصص' : 'Subject Course'));
                                $subSessionTitle = $sub->assignment?->session?->title ?: ($sub->assignment?->liveSession?->title ?: (app()->getLocale() === 'ar' ? 'الجلسة التفاعلية' : 'Interactive Session'));
                            @endphp
                            <div class="submission-record-card p-6 bg-slate-50/90 hover:bg-slate-100/80 rounded-3xl border border-slate-200/90 space-y-4 transition-all hover:shadow-md hover:-translate-y-0.5" data-course-id="{{ $subCourseId }}">
                                
                                {{-- Course & Session Context Header --}}
                                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200/70 pb-3 text-xs font-mono">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-teal-900 bg-teal-100/90 px-3 py-0.5 rounded-full border border-teal-200">
                                            📚 {{ $subCourseTitle }}
                                        </span>
                                        <span class="font-bold text-slate-800 bg-slate-200 px-3 py-0.5 rounded-full">
                                            📺 {{ $subSessionTitle }}
                                        </span>
                                    </div>
                                    <span class="text-slate-500">
                                        📅 {{ app()->getLocale() === 'ar' ? 'تاريخ التسليم' : 'Submitted' }}: <strong>{{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : 'Completed' }}</strong>
                                    </span>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <h4 class="font-bold text-base text-slate-900">{{ $sub->assignment?->title ?: (app()->getLocale() === 'ar' ? 'واجب الجلسة التفاعلية' : 'Session MSQ Assignment') }}</h4>
                                        <p class="text-xs font-mono text-slate-600 leading-relaxed">{{ $sub->evaluation_notes ?: ($sub->teacher_notes ?: (app()->getLocale() === 'ar' ? 'تم التقييم التلقائي بنجاح.' : 'Server evaluated submission cleanly.')) }}</p>
                                    </div>

                                    <div class="flex items-center gap-3 self-start sm:self-auto">
                                        <span class="text-base font-mono font-black text-slate-900 bg-white px-4 py-2 rounded-2xl border border-slate-200 shadow-2xs">
                                            {{ $sub->percentage !== null ? number_format($sub->percentage, 1) . '%' : ($sub->grade !== null ? number_format($sub->grade, 1) . '%' : 'Evaluated') }}
                                        </span>
                                        @if($sub->isPassed())
                                            <span class="text-xs font-mono font-extrabold bg-emerald-100 text-emerald-900 px-4 py-2 rounded-2xl border border-emerald-300 flex items-center gap-1.5 shadow-2xs">
                                                <span>✓</span> {{ app()->getLocale() === 'ar' ? 'ناجح' : 'PASSED' }}
                                            </span>
                                        @else
                                            <span class="text-xs font-mono font-extrabold bg-rose-100 text-rose-900 px-4 py-2 rounded-2xl border border-rose-300 flex items-center gap-1.5 shadow-2xs">
                                                <span>✕</span> {{ app()->getLocale() === 'ar' ? 'لم يجتاز' : 'FAILED' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500 text-center font-mono">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد تسليمات واجبات سابقة في السجل حتى الآن.' : 'No previous assignment submission history recorded yet.' }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Sidebar (Notifications & Exception Status) --}}
            <div class="lg:col-span-4 space-y-8">

                {{-- Notifications Feed --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                            <span>🔔</span> {{ __('app.portal.notifications') }}
                        </h3>
                        <span id="notifTotalAlerts" class="text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200/80 shadow-2xs">
                            {{ $userNotifications instanceof \Illuminate\Pagination\LengthAwarePaginator ? $userNotifications->total() : count($userNotifications) }} Alerts
                        </span>
                    </div>

                    <div class="space-y-3 transition-opacity duration-200" id="notificationsFeedContainer">
                        @forelse($userNotifications as $n)
                            <div class="p-4 bg-slate-50/90 hover:bg-slate-100/90 rounded-2xl border border-slate-200/90 space-y-1.5 shadow-2xs hover:-translate-y-0.5 hover:shadow-md transition-all">
                                <div class="flex justify-between items-center text-[11px] font-mono font-bold">
                                    @if($n->type === 'ASSIGNMENT_DEADLINE_REMINDER')
                                        <span class="text-amber-800 bg-amber-100/90 px-2.5 py-0.5 rounded-md border border-amber-200">⏰ Deadline 24h</span>
                                    @elseif($n->type === 'ADMIN_APPROVAL_ALERT')
                                        <span class="text-emerald-800 bg-emerald-100/90 px-2.5 py-0.5 rounded-md border border-emerald-200">✅ Admin Approved</span>
                                    @else
                                        <span class="text-teal-800 bg-teal-100/90 px-2.5 py-0.5 rounded-md border border-teal-200">🔔 Real-Time FCM Alert</span>
                                    @endif
                                    <span class="text-slate-400 font-normal">{{ $n->created_at ? $n->created_at->diffForHumans() : 'Just now' }}</span>
                                </div>
                                <h4 class="font-bold text-xs text-slate-900 leading-snug">{{ $n->title }}</h4>
                                <p class="text-xs text-slate-600 leading-relaxed font-mono">{{ $n->body }}</p>
                            </div>
                        @empty
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500 text-center font-mono space-y-1">
                                <div class="text-2xl">🔕</div>
                                <div>{{ app()->getLocale() === 'ar' ? 'لا توجد إشعارات مسجلة حالياً.' : 'No notifications in feed yet.' }}</div>
                            </div>
                        @endforelse
                    </div>


                    <div id="notificationsPaginationBar" class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-mono {{ $notifLastPage <= 1 ? 'hidden' : '' }}">
                        <button id="btnNotifPrev" onclick="fetchNotificationsPage(notifCurrentPage - 1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                            <span>&larr;</span> <span>{{ app()->getLocale() === 'ar' ? 'السابق' : 'Prev' }}</span>
                        </button>

                        <div class="text-slate-600 font-bold bg-slate-100/80 px-3.5 py-1 rounded-xl border border-slate-200/90 text-[11px] shadow-2xs">
                            {{ app()->getLocale() === 'ar' ? 'صفحة' : 'Page' }} <span id="notifCurrentPageText" class="text-teal-700 font-extrabold">{{ $notifCurrentPage }}</span> {{ app()->getLocale() === 'ar' ? 'من' : 'of' }} <span id="notifLastPageText" class="text-slate-800">{{ $notifLastPage }}</span>
                        </div>

                        <button id="btnNotifNext" onclick="fetchNotificationsPage(notifCurrentPage + 1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                            <span>{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}</span> <span>&rarr;</span>
                        </button>
                    </div>
                </div>

                <script>
                    // Real-Time Polling for Interactive Live Sessions & Demo Changes
                    setInterval(() => {
                        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(res => res.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContainer = doc.querySelector('#upcomingSessionsContainer');
                                const currentContainer = document.querySelector('#upcomingSessionsContainer');
                                if (newContainer && currentContainer && newContainer.innerHTML.trim() !== currentContainer.innerHTML.trim()) {
                                    currentContainer.innerHTML = newContainer.innerHTML;
                                }
                            }).catch(() => {});
                    }, 8000);
                </script>

                {{-- Submitted Exceptions List --}}
                <div id="exceptions" class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-5 animate-fade-in-up stagger-2 scroll-mt-28">
                    <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <span>📋</span> {{ __('app.portal.exceptions_history') }}
                    </h3>
                    <div class="space-y-3">
                        @forelse($exceptions as $exc)
                            <div class="p-4 bg-slate-50/90 hover:bg-slate-100/90 rounded-2xl border border-slate-200/90 space-y-1.5 shadow-2xs hover:-translate-y-0.5 hover:shadow-md transition-all">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-xs text-slate-900">
                                        {{ $exc->is_global || $exc->scope === 'global' ? (app()->getLocale() === 'ar' ? 'استثناء شامل (كل الكورسات)' : 'Global System Exemption') : (app()->getLocale() === 'ar' ? 'عذر كورس خاص' : 'Course Exception') }}
                                    </span>
                                    <span class="text-[10px] font-mono font-bold uppercase px-2.5 py-0.5 rounded-full {{ $exc->status === 'approved' ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : ($exc->status === 'rejected' ? 'bg-red-100 text-red-900 border border-red-200' : 'bg-amber-100 text-amber-900 border border-amber-200') }}">
                                        {{ $exc->status }}
                                    </span>
                                </div>
                                <p class="text-xs font-mono text-slate-600 truncate">{{ $exc->reason }}</p>
                            </div>
                        @empty
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500 text-center font-mono space-y-1">
                                <div class="text-xl">📋</div>
                                <div>{{ app()->getLocale() === 'ar' ? 'لا توجد طلبات استثناء سابقة.' : 'No previous exception requests found.' }}</div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- 1. Modal: Interactive MSQ Assignment Solver --}}
<div id="takeMsqModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl space-y-6 border border-slate-200 my-8">
        <div class="flex justify-between items-start pb-4 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-mono font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-full uppercase border border-teal-200">Interactive MSQ Evaluation</span>
                <h3 id="msqModalTitle" class="font-bold text-xl text-slate-900 mt-1">Loading Assignment...</h3>
                <p id="msqModalDesc" class="text-xs text-slate-500 font-mono"></p>
            </div>
            <button onclick="closeMsqModal()" class="text-slate-400 hover:text-slate-800 font-bold text-2xl cursor-pointer p-1">&times;</button>
        </div>

        {{-- Timer & Warning Banner --}}
        <div id="msqTimerBar" class="flex items-center justify-between p-4 bg-slate-900 text-white rounded-2xl text-xs font-mono shadow-md">
            <span class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                <span>Session Deadline Rule Active</span>
            </span>
            <span id="msqTimerDisplay" class="font-bold text-teal-300 text-sm">Time Remaining: --:--</span>
        </div>

        <form id="msqAnswerForm" class="space-y-6">
            @csrf
            <input type="hidden" id="msqAssignmentId" name="assignment_id" value="">

            <div id="msqQuestionsContainer" class="space-y-6 max-h-[50vh] overflow-y-auto pr-2">
                <div class="text-center py-8 text-slate-500 font-mono text-xs">Loading questions...</div>
            </div>

            <button type="submit" id="msqSubmitBtn" class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/30 touch-press font-bold py-3.5 rounded-2xl flex items-center justify-center gap-2">
                <span>Submit Assignment for Automated Evaluation</span> &rarr;
            </button>
        </form>
    </div>
</div>

{{-- 2. Modal: Submit Session Absence Excuse --}}
<div id="excuseModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4 border border-slate-200">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-900">{{ __('app.portal.submit_excuse') }}</h3>
            <button onclick="document.getElementById('excuseModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <div id="excuseAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

        <form id="excuseForm" action="{{ route('ajax.exception.submit') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="live_session_id" value="1">

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'سبب الاعتذار عن الحصة' : 'Reason for Absence' }}</label>
                <textarea name="reason" required minlength="10" placeholder="{{ app()->getLocale() === 'ar' ? 'اذكر سبب الغياب بالتفصيل...' : 'Provide detailed absence reason...' }}" class="input-mobile h-24"></textarea>
                <p class="text-[11px] text-amber-700 font-bold">⚠️ {{ __('app.sessions.excuse_2h_rule') }}</p>
            </div>

            <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md touch-press">
                {{ app()->getLocale() === 'ar' ? 'إرسال عذر الغياب' : 'Submit Absence Excuse' }}
            </button>
        </form>
    </div>
</div>

{{-- 3. Modal: Submit Homework Exception Request --}}
<div id="homeworkExceptionModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4 border border-slate-200">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-900">{{ __('app.portal.submit_exception') }}</h3>
            <button onclick="document.getElementById('homeworkExceptionModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <div id="hwExceptionAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

        <form id="hwExceptionForm" action="{{ route('ajax.exception.submit') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'نطاق الاستثناء' : 'Exception Scope' }}</label>
                <select name="scope" id="exceptionScopeSelect" class="input-mobile" onchange="document.getElementById('courseSelectGroup').style.display = this.value === 'global' ? 'none' : 'block'">
                    <option value="course">{{ app()->getLocale() === 'ar' ? 'كورس معين محدد' : 'Single Specific Course' }}</option>
                    <option value="global">{{ app()->getLocale() === 'ar' ? 'استثناء شامل لجميع الكورسات' : 'Global System Exception (All Enrolled Courses)' }}</option>
                </select>
            </div>

            <div id="courseSelectGroup" class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'اختر الكورس المستهدف' : 'Target Enrolled Course' }}</label>
                <select name="course_id" class="input-mobile">
                    @if(isset($enrollments) && count($enrollments) > 0)
                        @foreach($enrollments as $e)
                            <option value="{{ $e->course_id }}">{{ $e->course?->title ?: 'الكورس الدراسي' }}</option>
                        @endforeach
                    @else
                        <option value="1">Physics Electromagnetism & Circuits</option>
                    @endif
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'تفاصيل مشكلة الواجب أو سبب الاستثناء' : 'Homework Exception Details' }}</label>
                <textarea name="reason" required minlength="10" placeholder="{{ app()->getLocale() === 'ar' ? 'اشرح المشكلة التقنية أو سبب طلب استثناء الواجب...' : 'Describe technical issue or homework exception...' }}" class="input-mobile h-24"></textarea>
            </div>

            <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md touch-press">
                {{ app()->getLocale() === 'ar' ? 'إرسال طلب استثناء الواجب' : 'Submit Homework Exception' }}
            </button>
        </form>
    </div>
</div>

<script>
let msqTimerInterval = null;

async function openMsqAssignmentModal(assignmentId) {
    const modal = document.getElementById('takeMsqModal');
    const titleEl = document.getElementById('msqModalTitle');
    const descEl = document.getElementById('msqModalDesc');
    const container = document.getElementById('msqQuestionsContainer');
    const assignIdInput = document.getElementById('msqAssignmentId');
    
    assignIdInput.value = assignmentId;
    modal.classList.remove('hidden');
    container.innerHTML = '<div class="text-center py-8 text-slate-500 font-mono text-xs">Loading assignment questions...</div>';

    try {
        const baseUrl = "{{ url('/ajax/assignments') }}";
        const res = await fetch(`${baseUrl}/${assignmentId}/details`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (!res.ok || !data.success) {
            if (window.Toast) window.Toast.error(data.message || 'Error loading assignment details');
            closeMsqModal();
            return;
        }

        const assign = data.assignment;
        titleEl.textContent = assign.title;
        descEl.textContent = assign.description || 'Complete all MSQ questions before the timer runs out.';

        // Setup Timer
        let durationSecs = (assign.duration_minutes || 30) * 60;
        const timerDisplay = document.getElementById('msqTimerDisplay');
        if (msqTimerInterval) clearInterval(msqTimerInterval);

        msqTimerInterval = setInterval(() => {
            if (durationSecs <= 0) {
                clearInterval(msqTimerInterval);
                timerDisplay.textContent = 'TIME EXPIRED';
                timerDisplay.className = 'font-bold text-red-400 animate-pulse';
                if (window.Toast) window.Toast.warning('Assignment time expired!', 'Deadline Alert');
                return;
            }
            durationSecs--;
            const mins = Math.floor(durationSecs / 60);
            const secs = durationSecs % 60;
            timerDisplay.textContent = `Time Remaining: ${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }, 1000);

        if (!assign.questions || assign.questions.length === 0) {
            container.innerHTML = '<div class="p-6 text-center text-slate-500 font-mono text-xs bg-slate-50 rounded-2xl">No questions configured for this assignment yet.</div>';
            return;
        }

        let html = '';
        assign.questions.forEach((q, idx) => {
            const inputType = q.is_multiple_choice ? 'checkbox' : 'radio';
            html += `
                <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-slate-900">Q${idx + 1}. ${q.question_text || ''}</span>
                        <span class="text-[10px] font-mono font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md">${q.points || 1} Points</span>
                    </div>
                    ${q.image_path ? `<img src="${q.image_path}" class="max-h-48 rounded-xl border border-slate-200 my-2 object-contain">` : ''}
                    <div class="space-y-2 pt-1">
            `;

            (q.options || []).forEach(opt => {
                html += `
                    <label class="flex items-center gap-3 p-3 bg-white hover:bg-teal-50/50 rounded-xl border border-slate-200/90 cursor-pointer transition-colors text-xs font-semibold text-slate-800">
                        <input type="${inputType}" name="answers[${q.id}][]" value="${opt.id}" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <span>${opt.option_text || ''}</span>
                    </label>
                `;
            });

            html += `</div></div>`;
        });

        container.innerHTML = html;
    } catch (err) {
        if (window.Toast) window.Toast.error('Network error loading assignment');
        closeMsqModal();
    }
}

function closeMsqModal() {
    document.getElementById('takeMsqModal').classList.add('hidden');
    if (msqTimerInterval) clearInterval(msqTimerInterval);
}

document.addEventListener('DOMContentLoaded', function () {
    const excuseForm = document.getElementById('excuseForm');
    const excuseAlert = document.getElementById('excuseAlert');
    if (excuseForm) {
        excuseForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            excuseAlert.classList.add('hidden');
            const formData = new FormData(excuseForm);

            try {
                const res = await fetch(excuseForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                excuseAlert.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
                excuseAlert.textContent = data.message;
                excuseAlert.classList.remove('hidden');

                if (data.success) {
                    setTimeout(() => document.getElementById('excuseModal').classList.add('hidden'), 1500);
                }
            } catch (err) {
                excuseAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
                excuseAlert.textContent = 'Network error. Please try again.';
                excuseAlert.classList.remove('hidden');
            }
        });
    }

    const hwForm = document.getElementById('hwExceptionForm');
    const hwAlert = document.getElementById('hwExceptionAlert');
    if (hwForm) {
        hwForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            hwAlert.classList.add('hidden');
            const formData = new FormData(hwForm);

            try {
                const res = await fetch(hwForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                hwAlert.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
                hwAlert.textContent = data.message;
                hwAlert.classList.remove('hidden');

                if (data.success) {
                    setTimeout(() => document.getElementById('homeworkExceptionModal').classList.add('hidden'), 1500);
                }
            } catch (err) {
                hwAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
                hwAlert.textContent = 'Network error. Please try again.';
                hwAlert.classList.remove('hidden');
            }
        });
    }

    const msqForm = document.getElementById('msqAnswerForm');
    const submitBtn = document.getElementById('msqSubmitBtn');

    if (msqForm) {
        msqForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const formData = new FormData(msqForm);
                const res = await fetch("{{ route('ajax.assignment.submit') }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    if (window.Toast) window.Toast.error(data.message || 'Submission failed');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    return;
                }

                if (window.Toast) {
                    if (data.is_passed) {
                        window.Toast.success(`Score: ${data.percentage}% (PASSED ✓)`, 'Assignment Completed!');
                    } else {
                        window.Toast.error(`Score: ${data.percentage}% (FAILED ✕ - Passing: ${data.passing_score}%)`, 'Assignment Result');
                    }
                }

                closeMsqModal();
                setTimeout(() => window.location.reload(), 1200);
            } catch (err) {
                if (window.Toast) window.Toast.error('Network error submitting assignment');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    }
});

// ─────────────────────────────────────────────────────────────────────────
// Real-Time AJAX Notifications Pagination (No Page Refresh)
// ─────────────────────────────────────────────────────────────────────────
let notifCurrentPage = {{ $notifCurrentPage }};
let notifLastPage = {{ $notifLastPage }};

function updatePaginationControls(page, lastPage, total) {
    notifCurrentPage = page;
    notifLastPage = lastPage;

    const btnPrev = document.getElementById('btnNotifPrev');
    const btnNext = document.getElementById('btnNotifNext');
    const currText = document.getElementById('notifCurrentPageText');
    const lastText = document.getElementById('notifLastPageText');
    const totalAlerts = document.getElementById('notifTotalAlerts');
    const pagBar = document.getElementById('notificationsPaginationBar');

    if (currText) currText.textContent = page;
    if (lastText) lastText.textContent = lastPage;
    if (totalAlerts) totalAlerts.textContent = `${total} Alerts`;

    if (btnPrev) btnPrev.disabled = (page <= 1);
    if (btnNext) btnNext.disabled = (page >= lastPage);

    if (pagBar) {
        if (lastPage <= 1) {
            pagBar.classList.add('hidden');
        } else {
            pagBar.classList.remove('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    updatePaginationControls(notifCurrentPage, notifLastPage, {{ $totalAlertsCount }});
});

async function fetchNotificationsPage(page) {
    if (page < 1 || (notifLastPage && page > notifLastPage)) return;

    const container = document.getElementById('notificationsFeedContainer');
    if (container) container.classList.add('opacity-40');

    try {
        const res = await fetch(`{{ route('ajax.notifications.feed') }}?page=${page}&per_page=5`);
        const data = await res.json();

        if (!data.success) {
            if (container) container.classList.remove('opacity-40');
            return;
        }

        if (container) {
            container.innerHTML = '';
            if (data.notifications && data.notifications.length > 0) {
                data.notifications.forEach(n => {
                    const card = document.createElement('div');
                    card.className = 'p-4 bg-slate-50/90 hover:bg-slate-100/90 rounded-2xl border border-slate-200/90 space-y-1.5 shadow-2xs hover:-translate-y-0.5 hover:shadow-md transition-all';
                    
                    let badgeHtml = '<span class="text-teal-800 bg-teal-100/90 px-2.5 py-0.5 rounded-md border border-teal-200">🔔 Real-Time FCM Alert</span>';
                    if (n.type === 'ASSIGNMENT_DEADLINE_REMINDER') {
                        badgeHtml = '<span class="text-amber-800 bg-amber-100/90 px-2.5 py-0.5 rounded-md border border-amber-200">⏰ Deadline 24h</span>';
                    } else if (n.type === 'ADMIN_APPROVAL_ALERT') {
                        badgeHtml = '<span class="text-emerald-800 bg-emerald-100/90 px-2.5 py-0.5 rounded-md border border-emerald-200">✅ Admin Approved</span>';
                    }

                    const timeStr = n.created_at ? formatTimeAgo(n.created_at) : 'Just now';

                    card.innerHTML = `
                        <div class="flex justify-between items-center text-[11px] font-mono font-bold">
                            ${badgeHtml}
                            <span class="text-slate-400 font-normal">${timeStr}</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-900 leading-snug">${escapeHtml(n.title)}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed font-mono">${escapeHtml(n.body)}</p>
                    `;
                    container.appendChild(card);
                });
            } else {
                container.innerHTML = `
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500 text-center font-mono space-y-1">
                        <div class="text-2xl">🔕</div>
                        <div>${@json(app()->getLocale() === 'ar' ? 'لا توجد إشعارات مسجلة حالياً.' : 'No notifications in feed yet.')}</div>
                    </div>
                `;
            }
            container.classList.remove('opacity-40');
        }

        if (data.pagination) {
            updatePaginationControls(data.pagination.current_page, data.pagination.last_page, data.pagination.total);
        }

    } catch (err) {
        if (container) container.classList.remove('opacity-40');
    }
}

function formatTimeAgo(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} minutes ago`;
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours} hours ago`;
    return `${Math.floor(diffHours / 24)} days ago`;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
}
</script>

{{-- Ultra-Premium Glassmorphic Enrolled Course Details Modal --}}
<div id="enrolledCourseModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 overflow-y-auto animate-fade-in">
    <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl border border-slate-200/90 flex flex-col my-auto relative">
        
        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white p-6 sm:p-8 flex items-start justify-between relative overflow-hidden shrink-0">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="space-y-2 relative z-10">
                <div class="flex flex-wrap items-center gap-2">
                    <span id="modalCourseSubject" class="bg-teal-600 text-white text-xs font-bold px-3 py-0.5 rounded-full shadow-xs"></span>
                    <span id="modalCourseGrade" class="bg-slate-800 text-slate-300 text-xs font-mono px-2.5 py-0.5 rounded-full border border-slate-700"></span>
                    <span class="bg-emerald-500/20 text-emerald-300 text-xs font-mono font-bold px-2.5 py-0.5 rounded-full border border-emerald-500/40">✓ {{ app()->getLocale() === 'ar' ? 'مشترك بالنظام' : 'Enrolled' }}</span>
                </div>
                <h2 id="modalCourseTitle" class="font-heading font-black text-2xl sm:text-3xl text-white tracking-tight"></h2>
                <p id="modalCourseTeacher" class="text-xs font-mono text-teal-300 flex items-center gap-1.5"></p>
            </div>
            <button onclick="closeEnrolledCourseModal()" class="w-10 h-10 rounded-full bg-slate-800/80 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center font-bold text-lg transition-all cursor-pointer border border-slate-700 shrink-0 relative z-10">
                ✕
            </button>
        </div>

        {{-- Modal Scrollable Body --}}
        <div class="p-6 sm:p-8 space-y-6 overflow-y-auto max-h-[calc(90vh-180px)] font-mono text-slate-800">
            {{-- Overview Card --}}
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500">
                    {{ app()->getLocale() === 'ar' ? 'نبذة عن الكورس والمحتوى' : 'Course Overview & Learning Objectives' }}
                </h4>
                <p id="modalCourseDesc" class="text-xs text-slate-700 leading-relaxed"></p>
            </div>

            {{-- Tabs / Section Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h3 class="font-heading font-black text-lg text-slate-900 flex items-center gap-2">
                    <span>📺</span> {{ app()->getLocale() === 'ar' ? 'منهج الكورس والحصص التفصيلية' : 'Full Curriculum & Session Modules' }}
                </h3>
                <span id="modalTotalSessionsBadge" class="text-xs font-bold bg-teal-100 text-teal-900 px-3 py-1 rounded-full border border-teal-200"></span>
            </div>

            {{-- Live Sessions Subsection --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-teal-800 flex items-center gap-1.5">
                        <span>🟢</span> {{ app()->getLocale() === 'ar' ? 'جدول الحصص والبث المباشر (Live Streams)' : 'Live Stream Schedule' }}
                    </h4>
                    <span id="liveSessionsCountBadge" class="text-[11px] font-mono text-slate-500 font-bold"></span>
                </div>
                <div id="modalLiveSessionsList" class="space-y-2.5"></div>
                {{-- Live Sessions Pagination Bar --}}
                <div id="modalLivePaginationBar" class="hidden flex items-center justify-between pt-2 text-xs font-mono text-slate-600 border-t border-slate-100">
                    <button id="btnLivePrev" onclick="changeModalLivePage(-1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                        <span>&larr;</span> <span>{{ app()->getLocale() === 'ar' ? 'السابق' : 'Prev' }}</span>
                    </button>
                    <span id="modalLivePageText" class="font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-xl border border-teal-200">Page 1 of 1</span>
                    <button id="btnLiveNext" onclick="changeModalLivePage(1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                        <span>{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}</span> <span>&rarr;</span>
                    </button>
                </div>
            </div>

            {{-- Recorded Course Sessions Subsection --}}
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-indigo-800 flex items-center gap-1.5">
                        <span>📹</span> {{ app()->getLocale() === 'ar' ? 'دروس الفيديو والواجبات المنهجية (Recorded Modules & MSQs)' : 'Recorded Modules & Assignments' }}
                    </h4>
                    <span id="recSessionsCountBadge" class="text-[11px] font-mono text-slate-500 font-bold"></span>
                </div>
                <div id="modalRecordedSessionsList" class="space-y-2.5"></div>
                {{-- Recorded Sessions Pagination Bar --}}
                <div id="modalRecPaginationBar" class="hidden flex items-center justify-between pt-2 text-xs font-mono text-slate-600 border-t border-slate-100">
                    <button id="btnRecPrev" onclick="changeModalRecPage(-1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                        <span>&larr;</span> <span>{{ app()->getLocale() === 'ar' ? 'السابق' : 'Prev' }}</span>
                    </button>
                    <span id="modalRecPageText" class="font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-xl border border-indigo-200">Page 1 of 1</span>
                    <button id="btnRecNext" onclick="changeModalRecPage(1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                        <span>{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}</span> <span>&rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center text-xs font-mono text-slate-500 shrink-0">
            <span>🎓 Elite Academy Certified Curriculum</span>
            <button onclick="closeEnrolledCourseModal()" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold cursor-pointer transition-all">
                {{ app()->getLocale() === 'ar' ? 'إغلاق النافذة' : 'Close' }}
            </button>
        </div>
    </div>
</div>


<script>
window.enrolledCoursesData = @json($enrolledCoursesDataMap);

let currentModalCourseId = null;
let currentModalLivePage = 1;
let currentModalRecPage = 1;
const MODAL_LIVE_PER_PAGE = 3;
const MODAL_REC_PER_PAGE = 3;

function openEnrolledCourseModal(courseId) {
    const data = window.enrolledCoursesData[courseId];
    if (!data) return;

    currentModalCourseId = courseId;
    currentModalLivePage = 1;
    currentModalRecPage = 1;

    document.getElementById('modalCourseTitle').textContent = data.title;
    document.getElementById('modalCourseSubject').textContent = data.subject;
    document.getElementById('modalCourseGrade').textContent = data.grade;
    document.getElementById('modalCourseTeacher').textContent = '👨‍🏫 ' + (data.teacher || 'Dr. Instructor');
    document.getElementById('modalCourseDesc').textContent = data.description;

    const liveCount = data.live_sessions ? data.live_sessions.length : 0;
    const recCount = data.recorded_sessions ? data.recorded_sessions.length : 0;
    document.getElementById('modalTotalSessionsBadge').textContent = (liveCount + recCount) + ' Sessions Total';
    
    document.getElementById('liveSessionsCountBadge').textContent = liveCount + ' ' + @json(app()->getLocale() === 'ar' ? 'بث مباشر' : 'streams');
    document.getElementById('recSessionsCountBadge').textContent = recCount + ' ' + @json(app()->getLocale() === 'ar' ? 'دروس مسجلة' : 'modules');

    renderModalLiveSessions();
    renderModalRecordedSessions();

    const modal = document.getElementById('enrolledCourseModal');
    modal.classList.remove('hidden');
}

function renderModalLiveSessions() {
    const data = window.enrolledCoursesData[currentModalCourseId];
    if (!data) return;

    const liveContainer = document.getElementById('modalLiveSessionsList');
    const pagBar = document.getElementById('modalLivePaginationBar');
    liveContainer.innerHTML = '';

    const list = data.live_sessions || [];
    const total = list.length;

    if (total === 0) {
        liveContainer.innerHTML = `<div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-500 text-center font-mono">${@json(app()->getLocale() === 'ar' ? 'لا توجد جلسات بث مباشر مجدولة لهذا الكورس حالياً.' : 'No live streams currently scheduled for this course.')}</div>`;
        if (pagBar) pagBar.classList.add('hidden');
        return;
    }

    const lastPage = Math.ceil(total / MODAL_LIVE_PER_PAGE);
    if (currentModalLivePage < 1) currentModalLivePage = 1;
    if (currentModalLivePage > lastPage) currentModalLivePage = lastPage;

    const startIdx = (currentModalLivePage - 1) * MODAL_LIVE_PER_PAGE;
    const endIdx = startIdx + MODAL_LIVE_PER_PAGE;
    const pageItems = list.slice(startIdx, endIdx);

    pageItems.forEach(ls => {
        const card = document.createElement('div');
        card.className = 'p-3.5 bg-slate-50 hover:bg-slate-100/90 rounded-2xl border border-slate-200 space-y-2 transition-all';
        
        let btnHtml = `<span class="text-[11px] font-bold px-3 py-1 rounded-xl bg-slate-200 text-slate-700">${escapeHtml(ls.state_label)}</span>`;
        if (ls.can_join && ls.meeting_link) {
            btnHtml = `<a href="${escapeHtml(ls.meeting_link)}" target="_blank" class="btn-lift text-[11px] font-bold px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs">🟢 Join Stream</a>`;
        }

        card.innerHTML = `
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full ${ls.is_live ? 'bg-emerald-500 animate-ping' : 'bg-slate-400'}"></span>
                    <span class="font-bold text-xs text-slate-900">${escapeHtml(ls.title)}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-500">📅 ${escapeHtml(ls.start_at)}</span>
                    ${btnHtml}
                </div>
            </div>
        `;
        liveContainer.appendChild(card);
    });

    if (pagBar) {
        if (lastPage > 1) {
            pagBar.classList.remove('hidden');
            document.getElementById('modalLivePageText').textContent = @json(app()->getLocale() === 'ar' ? 'صفحة' : 'Page') + ` ${currentModalLivePage} ` + @json(app()->getLocale() === 'ar' ? 'من' : 'of') + ` ${lastPage}`;
            document.getElementById('btnLivePrev').disabled = (currentModalLivePage <= 1);
            document.getElementById('btnLiveNext').disabled = (currentModalLivePage >= lastPage);
        } else {
            pagBar.classList.add('hidden');
        }
    }
}

function changeModalLivePage(delta) {
    currentModalLivePage += delta;
    renderModalLiveSessions();
}

function renderModalRecordedSessions() {
    const data = window.enrolledCoursesData[currentModalCourseId];
    if (!data) return;

    const recContainer = document.getElementById('modalRecordedSessionsList');
    const pagBar = document.getElementById('modalRecPaginationBar');
    recContainer.innerHTML = '';

    const list = data.recorded_sessions || [];
    const total = list.length;

    if (total === 0) {
        recContainer.innerHTML = `<div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-500 text-center font-mono">${@json(app()->getLocale() === 'ar' ? 'لا توجد دروس فيديو مسجلة منشورة لهذا الكورس حالياً.' : 'No recorded video lessons published for this course yet.')}</div>`;
        if (pagBar) pagBar.classList.add('hidden');
        return;
    }

    const lastPage = Math.ceil(total / MODAL_REC_PER_PAGE);
    if (currentModalRecPage < 1) currentModalRecPage = 1;
    if (currentModalRecPage > lastPage) currentModalRecPage = lastPage;

    const startIdx = (currentModalRecPage - 1) * MODAL_REC_PER_PAGE;
    const endIdx = startIdx + MODAL_REC_PER_PAGE;
    const pageItems = list.slice(startIdx, endIdx);

    pageItems.forEach(rs => {
        const card = document.createElement('div');
        card.className = 'p-4 bg-slate-50 hover:bg-slate-100/90 rounded-2xl border border-slate-200 space-y-2.5 transition-all';
        
        let assignHtml = '';
        if (rs.assignments && rs.assignments.length > 0) {
            assignHtml = `<div class="pt-2 border-t border-slate-200/60 flex flex-wrap items-center gap-2">`;
            rs.assignments.forEach(a => {
                assignHtml += `<a href="${escapeHtml(a.url)}" class="btn-lift inline-flex items-center gap-1 text-[11px] font-bold bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 px-2.5 py-1 rounded-lg">📝 ${escapeHtml(a.title)} (${a.points} pts) &rarr;</a>`;
            });
            assignHtml += `</div>`;
        }

        card.innerHTML = `
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase bg-slate-200 text-slate-700 px-2 py-0.5 rounded">Module ${rs.index}</span>
                        <span class="font-bold text-xs text-slate-900">${escapeHtml(rs.title)}</span>
                    </div>
                    ${rs.description ? `<p class="text-[11px] text-slate-600 line-clamp-1">${escapeHtml(rs.description)}</p>` : ''}
                </div>
                <span class="text-[11px] text-slate-500 font-bold bg-white px-2.5 py-1 rounded-lg border border-slate-200">⏱️ ${rs.duration} mins</span>
            </div>
            ${assignHtml}
        `;
        recContainer.appendChild(card);
    });

    if (pagBar) {
        if (lastPage > 1) {
            pagBar.classList.remove('hidden');
            document.getElementById('modalRecPageText').textContent = @json(app()->getLocale() === 'ar' ? 'صفحة' : 'Page') + ` ${currentModalRecPage} ` + @json(app()->getLocale() === 'ar' ? 'من' : 'of') + ` ${lastPage}`;
            document.getElementById('btnRecPrev').disabled = (currentModalRecPage <= 1);
            document.getElementById('btnRecNext').disabled = (currentModalRecPage >= lastPage);
        } else {
            pagBar.classList.add('hidden');
        }
    }
}

function changeModalRecPage(delta) {
    currentModalRecPage += delta;
    renderModalRecordedSessions();
}

function closeEnrolledCourseModal() {
    const modal = document.getElementById('enrolledCourseModal');
    modal.classList.add('hidden');
}

// ── Live Session Countdown Timers ──────────────────────────────────────────
function initSessionCountdowns() {
    const isAr = @json(app()->getLocale() === 'ar');

    function update() {
        const now = new Date().getTime();

        // Single live ticking countdown to session start time
        document.querySelectorAll('.session-countdown-pill').forEach(pill => {
            const startStr = pill.getAttribute('data-start-time');
            const textEl = pill.querySelector('.countdown-text');
            if (!startStr || !textEl) return;

            const startTime = new Date(startStr).getTime();
            const diff = startTime - now;

            if (diff <= 0) {
                textEl.textContent = isAr ? 'بدأت الحصة الآن 🔴' : 'Session Live Now 🔴';
                return;
            }

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            let parts = [];
            if (d > 0) parts.push(d + (isAr ? 'ي ' : 'd '));
            if (h > 0 || d > 0) parts.push(String(h).padStart(2, '0') + (isAr ? 'س ' : 'h '));
            parts.push(String(m).padStart(2, '0') + (isAr ? 'د ' : 'm '));
            parts.push(String(s).padStart(2, '0') + (isAr ? 'ث' : 's'));

            textEl.textContent = (isAr ? 'تبدأ خلال: ' : 'Starts in: ') + parts.join('');
        });
    }

    update();
    setInterval(update, 1000);
}

function switchPortalSection(sectionId) {
    const el = document.getElementById(sectionId);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    document.querySelectorAll('.portal-nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('href') === `#${sectionId}`) {
            item.classList.add('active');
        }
    });
    // Only close sidebar drawer on mobile/tablet screens (< 1024px)
    if (window.innerWidth < 1024 && typeof togglePortalSidebar === 'function') {
        togglePortalSidebar(false);
    }
}

function filterAssignmentsByCourse(courseId) {
    document.querySelectorAll('.assign-filter-btn').forEach(btn => {
        btn.classList.remove('bg-teal-600', 'text-white', 'shadow-xs');
        btn.classList.add('bg-slate-100', 'text-slate-700');
        if (btn.getAttribute('data-course') == courseId) {
            btn.classList.remove('bg-slate-100', 'text-slate-700');
            btn.classList.add('bg-teal-600', 'text-white', 'shadow-xs');
        }
    });
    document.querySelectorAll('.available-assign-card').forEach(card => {
        if (courseId === 'all' || card.getAttribute('data-course-id') == courseId || card.getAttribute('data-course-id') == '0') {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

function filterSubmissionsByCourse(courseId) {
    document.querySelectorAll('.sub-filter-btn').forEach(btn => {
        btn.classList.remove('bg-teal-600', 'text-white', 'shadow-xs');
        btn.classList.add('bg-slate-100', 'text-slate-700');
        if (btn.getAttribute('data-course') == courseId) {
            btn.classList.remove('bg-slate-100', 'text-slate-700');
            btn.classList.add('bg-teal-600', 'text-white', 'shadow-xs');
        }
    });
    document.querySelectorAll('.submission-record-card').forEach(card => {
        if (courseId === 'all' || card.getAttribute('data-course-id') == courseId || card.getAttribute('data-course-id') == '0') {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSessionCountdowns);
} else {
    initSessionCountdowns();
}
</script>
@endsection
