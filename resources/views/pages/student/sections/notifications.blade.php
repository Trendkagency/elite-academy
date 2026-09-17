{{-- SECTION 8: NOTIFICATIONS FEED & TEACHER PEDAGOGICAL NOTES --}}
<section id="sectionPane_notifications" class="student-section-pane space-y-6 {{ $activeTab === 'notifications' ? '' : 'hidden' }}">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Left 6 Cols: Notifications Center --}}
        <div class="lg:col-span-6 bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <div class="flex items-center gap-2">
                    <h3 class="font-heading font-black text-base sm:text-lg md:text-xl text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-bell text-teal-600 dark:text-teal-400"></i>
                        <span>{{ __('app.portal.notifications') }}</span>
                    </h3>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openStudentPreviewGuide('notifications')"
                        class="btn-lift px-3 py-1 bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-200 text-slate-950 rounded-xl text-xs font-black shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap transition-all">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-950 text-xs animate-pulse"></i>
                        <span>{{ $isAr ? 'معاينة وشرح' : 'Preview' }}</span>
                    </button>
                    <span id="notifTotalAlerts" class="text-xs font-mono font-bold text-teal-800 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/60 px-3 py-1 rounded-full border border-teal-200/80 dark:border-teal-800">
                        {{ $totalAlertsCount }} Alerts
                    </span>
                </div>
            </div>

            <div class="space-y-3 transition-opacity duration-200" id="notificationsFeedContainer">
                @forelse($userNotifications as $n)
                    <div class="p-4 bg-slate-50/90 dark:bg-slate-800/80 hover:bg-slate-100/90 dark:hover:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 space-y-1.5 shadow-2xs transition-all">
                        <div class="flex justify-between items-center text-[11px] font-mono font-bold">
                            @if($n->type === 'ASSIGNMENT_DEADLINE_REMINDER')
                                <span class="text-amber-800 dark:text-amber-300 bg-amber-100/90 dark:bg-amber-950/60 px-2.5 py-0.5 rounded-md border border-amber-200 dark:border-amber-800">
                                    <i class="fa-solid fa-clock"></i> Deadline 24h
                                </span>
                            @elseif($n->type === 'ADMIN_APPROVAL_ALERT')
                                <span class="text-emerald-800 dark:text-emerald-300 bg-emerald-100/90 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i> Admin Approved
                                </span>
                            @else
                                <span class="text-teal-800 dark:text-teal-300 bg-teal-100/90 dark:bg-teal-950/60 px-2.5 py-0.5 rounded-md border border-teal-200 dark:border-teal-800">
                                    <i class="fa-solid fa-bell"></i> Real-Time FCM Alert
                                </span>
                            @endif
                            <span class="text-slate-400 font-normal">{{ $n->created_at ? $n->created_at->diffForHumans() : 'Just now' }}</span>
                        </div>
                        <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white leading-snug">{{ $n->title }}</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-mono">{{ $n->body }}</p>
                    </div>
                @empty
                    <div class="p-6 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs text-slate-500 text-center font-mono space-y-1">
                        <div class="text-2xl"><i class="fa-solid fa-bell-slash"></i></div>
                        <div>{{ $isAr ? 'لا توجد إشعارات مسجلة حالياً.' : 'No notifications in feed yet.' }}</div>
                    </div>
                @endforelse
            </div>

            {{-- Notifications Pagination Bar --}}
            <div id="notificationsPaginationBar" class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-mono {{ $notifLastPage <= 1 ? 'hidden' : '' }}">
                <button id="btnNotifPrev" onclick="fetchNotificationsPage(notifCurrentPage - 1)"
                    class="px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 text-slate-700 dark:text-slate-200 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                    <span>&larr;</span> <span>{{ $isAr ? 'السابق' : 'Prev' }}</span>
                </button>

                <div class="text-slate-600 dark:text-slate-400 font-bold bg-slate-100 dark:bg-slate-800 px-3.5 py-1 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px]">
                    {{ $isAr ? 'صفحة' : 'Page' }} <span id="notifCurrentPageText" class="text-teal-700 dark:text-teal-300 font-extrabold">{{ $notifCurrentPage }}</span>
                    {{ $isAr ? 'من' : 'of' }} <span id="notifLastPageText" class="text-slate-800 dark:text-slate-200">{{ $notifLastPage }}</span>
                </div>

                <button id="btnNotifNext" onclick="fetchNotificationsPage(notifCurrentPage + 1)"
                    class="px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 text-slate-700 dark:text-slate-200 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                    <span>{{ $isAr ? 'التالي' : 'Next' }}</span> <span>&rarr;</span>
                </button>
            </div>
        </div>

        {{-- Right 6 Cols: Teacher Pedagogical Notes --}}
        <div class="lg:col-span-6 bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <h3 class="font-heading font-black text-base sm:text-lg md:text-xl text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="text-teal-600 dark:text-teal-400"><i class="fa-solid fa-comments"></i></span>
                    <span>{{ $isAr ? 'ملاحظات المعلمين والتوجيه الأكاديمي' : 'Teacher Notes & Feedback' }}</span>
                </h3>
                <span class="px-2.5 py-1 bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 border border-teal-200/80 dark:border-teal-800 text-[11px] font-mono font-extrabold rounded-xl shrink-0">
                    {{ count($teacherNotes) }} {{ $isAr ? 'ملاحظات' : 'Notes' }}
                </span>
            </div>

            <div class="space-y-3.5 max-h-[500px] overflow-y-auto custom-scrollbar">
                @forelse($teacherNotes as $tn)
                    @php
                        $catConfig = match ($tn->category) {
                            'academic' => ['label' => ($isAr ? 'أكاديمي' : 'Academic'), 'bg' => 'bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 border-teal-200 dark:border-teal-800'],
                            'homework' => ['label' => ($isAr ? 'الواجبات' : 'Homework'), 'bg' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800'],
                            'participation' => ['label' => ($isAr ? 'المشاركة' : 'Participation'), 'bg' => 'bg-purple-50 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border-purple-200 dark:border-purple-800'],
                            'behavior' => ['label' => ($isAr ? 'السلوك والانضباط' : 'Behavior'), 'bg' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800'],
                            default => ['label' => ($isAr ? 'توجيه عام' : 'General'), 'bg' => 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'],
                        };
                    @endphp
                    <div class="p-4 bg-slate-50/90 dark:bg-slate-800/80 hover:bg-slate-100/90 dark:hover:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 space-y-2.5 shadow-2xs transition-all">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xs flex items-center justify-center shrink-0">
                                    {{ mb_substr($tn->teacherProfile?->user?->name ?? 'T', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $tn->teacherProfile?->user?->name ?: __('Academic Teacher') }}
                                    </p>
                                    <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400 truncate">
                                        {{ $tn->teacherProfile?->subjects?->pluck('name')->join(', ') ?: 'Elite Faculty' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2.5 py-0.5 text-[10px] font-mono font-bold rounded-full border {{ $catConfig['bg'] }}">
                                    {{ $catConfig['label'] }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $tn->created_at ? $tn->created_at->diffForHumans() : '' }}</span>
                            </div>
                        </div>
                        <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/60 dark:border-slate-700/80 text-xs font-mono text-slate-800 dark:text-slate-200 leading-relaxed">
                            {{ $tn->note }}
                        </div>
                    </div>
                @empty
                    <div class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 text-center space-y-2">
                        <div class="text-2xl text-slate-400"><i class="fa-solid fa-comments"></i></div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                            {{ $isAr ? 'لا توجد ملاحظات مسجلة لك من المعلمين حتى الآن.' : 'No pedagogical notes recorded yet.' }}
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
