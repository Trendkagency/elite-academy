{{-- STUDENT OWN PACKAGE DETAILS MODAL DIALOG --}}
<div id="studentOwnPackageModal" class="elite-modal hidden" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200/90 dark:border-slate-800 space-y-6 text-start">
        
        {{-- Modal Header --}}
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg border border-teal-500/30">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 dark:text-white leading-tight">
                        {{ $isAr ? 'تفاصيل باقتي التعليمية' : 'My Package Details' }}
                    </h3>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'بيانات رصيد الحصص، الاستهلاك، وصلاحية الاشتراك الخاص بك' : 'Breakdown of your active credits, consumption, and validity' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openStudentPreviewGuide('packages')"
                    class="btn-lift px-3 py-1.5 bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-200 text-slate-950 rounded-xl text-xs font-black shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap transition-all">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-950 text-xs animate-pulse"></i>
                    <span>{{ $isAr ? 'شرح الرصيد' : 'Credits Guide' }}</span>
                </button>
                <button type="button" onclick="window.closeModal ? window.closeModal('studentOwnPackageModal') : document.getElementById('studentOwnPackageModal').classList.add('hidden')"
                    class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" aria-label="Close">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
        </div>

        @if($package)
            @php
                $usedPct = $package->total_sessions > 0 ? min(100, round(($package->used_sessions / $package->total_sessions) * 100)) : 0;
            @endphp
            {{-- Package Name & Status Card --}}
            <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="space-y-0.5">
                        <h4 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white">
                            {{ $package->packageTemplate?->name ?: ($isAr ? 'باقة تعليمية معتمدة' : 'Standard Student Package') }}
                        </h4>
                        <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">
                            {{ $isAr ? 'رقم الاشتراك:' : 'Package ID:' }} <strong class="text-slate-700 dark:text-slate-300">#PKG-{{ $package->id }}</strong>
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 {{ $hasActivePackage ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30' }}">
                        <span class="w-2 h-2 rounded-full {{ $hasActivePackage ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                        <span>{{ $hasActivePackage ? ($isAr ? 'باقة نشطة ومفعلة' : 'Active Package') : ($isAr ? 'منتهية / غير نشطة' : 'Inactive / Expired') }}</span>
                    </span>
                </div>

                {{-- Progress Bar --}}
                <div class="space-y-1.5 pt-2">
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">{{ $isAr ? 'نسبة استهلاك الحصص:' : 'Credits Consumed:' }}</span>
                        <span class="font-extrabold text-teal-600 dark:text-teal-400">{{ $usedPct }}% ({{ $package->used_sessions }}/{{ $package->total_sessions }} {{ $isAr ? 'حصة' : 'sessions' }})</span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ max(4, $usedPct) }}%;"></div>
                    </div>
                </div>
            </div>

            {{-- 3 Key Numeric Metric Cards --}}
            <div class="grid grid-cols-3 gap-3">
                {{-- Total Sessions --}}
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-center space-y-1">
                    <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400 block">{{ $isAr ? 'إجمالي الحصص' : 'Total Credits' }}</span>
                    <p class="font-heading font-black text-lg sm:text-xl text-slate-900 dark:text-white">{{ $package->total_sessions }}</p>
                    <span class="text-[10px] font-mono text-slate-400">{{ $isAr ? 'حصة دراسية' : 'Sessions' }}</span>
                </div>

                {{-- Used Sessions --}}
                <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-center space-y-1">
                    <span class="text-[11px] font-mono text-amber-800 dark:text-amber-300 block">{{ $isAr ? 'المستهلكة' : 'Used' }}</span>
                    <p class="font-heading font-black text-lg sm:text-xl text-amber-900 dark:text-amber-200">{{ $package->used_sessions }}</p>
                    <span class="text-[10px] font-mono text-amber-700 dark:text-amber-300">{{ $isAr ? 'حصة منفذة' : 'Deducted' }}</span>
                </div>

                {{-- Remaining Sessions --}}
                <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center space-y-1">
                    <span class="text-[11px] font-mono text-emerald-800 dark:text-emerald-300 block">{{ $isAr ? 'المتبقية' : 'Remaining' }}</span>
                    <p class="font-heading font-black text-lg sm:text-xl text-emerald-700 dark:text-emerald-300">{{ $package->remaining_sessions }}</p>
                    <span class="text-[10px] font-mono text-emerald-700 dark:text-emerald-400">{{ $isAr ? 'حصة متاحة' : 'Available' }}</span>
                </div>
            </div>

            {{-- Expiry & Guidelines Info --}}
            <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 space-y-2 text-xs font-mono">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-check text-teal-600 dark:text-teal-400"></i>
                        <span>{{ $isAr ? 'تاريخ انتهاء الصلاحية:' : 'Valid Until:' }}</span>
                    </span>
                    <strong class="text-slate-800 dark:text-slate-200">
                        {{ $package->expires_at ? $package->expires_at->format('Y-m-d') : ($isAr ? 'مفتوحة الصلاحية' : 'Unlimited') }}
                    </strong>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-indigo-500"></i>
                        <span>{{ $isAr ? 'قاعدة الخصم والاسترداد:' : 'Deduction Rule:' }}</span>
                    </span>
                    <span class="text-slate-700 dark:text-slate-300 font-semibold">
                        {{ $isAr ? 'تخصم الحصة عند الحضور، وتسترد في حال قبول العذر' : 'Deducted on attendance, refunded on approved excuse' }}
                    </span>
                </div>
            </div>
        @else
            <div class="p-8 text-center text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                <div class="text-3xl text-slate-400"><i class="fa-solid fa-credit-card"></i></div>
                <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">{{ $isAr ? 'لا توجد بيانات باقة مسجلة لك حالياً' : 'No Active Package Subscription' }}</h4>
                <p class="text-xs font-mono text-slate-500 max-w-sm mx-auto">
                    {{ $isAr ? 'يمكنك تصفح باقات الأكاديمية والاشتراك لبدء حضور الحصص.' : 'Subscribe to an academic plan to access live streams and classes.' }}
                </p>
                <a href="{{ route('courses') }}" class="btn-lift inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold font-mono">
                    <i class="fa-solid fa-graduation-cap"></i> {{ $isAr ? 'تصفح الكورسات' : 'Browse Courses' }}
                </a>
            </div>
        @endif

        {{-- Modal Footer --}}
        <div class="flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800 pt-4">
            <button type="button" onclick="window.closeModal ? window.closeModal('studentOwnPackageModal') : document.getElementById('studentOwnPackageModal').classList.add('hidden')"
                class="px-5 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                {{ $isAr ? 'إغلاق' : 'Close' }}
            </button>
        </div>
    </div>
</div>

