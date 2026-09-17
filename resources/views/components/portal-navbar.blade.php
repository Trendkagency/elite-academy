@php
    $user = auth()->user();
    $role = $user ? $user->getRoleName() : 'student';
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $otherLocale = $isAr ? 'en' : 'ar';
    $otherLocaleName = $isAr ? 'English (EN)' : 'العربية (AR)';
@endphp

<!-- Master Portal Navbar -->
<header class="sticky top-0 z-30 h-16 sm:h-18 lg:h-20 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 px-3 sm:px-5 lg:px-8 flex items-center justify-between gap-2 sm:gap-4 shadow-xs transition-all duration-200">
    
    <!-- Left: Hamburger Toggle (Mobile/Collapsed) + Breadcrumbs & Dynamic Title -->
    <div class="flex items-center gap-2.5 sm:gap-4 min-w-0 flex-1">
        <button type="button" 
                onclick="togglePortalSidebar(true)" 
                class="portal-navbar-toggle shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-teal-950/60 hover:text-teal-600 dark:hover:text-teal-400 flex items-center justify-center transition-all border border-slate-200/60 dark:border-slate-700/60 shadow-2xs cursor-pointer active:scale-95" 
                aria-label="{{ $isAr ? 'فتح القائمة الجانبية' : 'Open Navigation Sidebar' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="flex flex-col min-w-0">
            {{-- Breadcrumb Trail --}}
            <div class="hidden sm:flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 font-mono leading-none pb-0.5">
                <a href="{{ route('home') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-house text-[10px]"></i>
                    <span>{{ __('navbar.home') }}</span>
                </a>
                <span class="text-slate-400 dark:text-slate-600">/</span>
                <span class="text-teal-600 dark:text-teal-400 font-bold flex items-center gap-1">
                    @if($role === 'teacher')
                        <i class="fa-solid fa-chalkboard-user text-[10px]"></i> {{ __('app.teacher_portal') }}
                    @elseif($role === 'parent')
                        <i class="fa-solid fa-users text-[10px]"></i> {{ __('app.parent_portal') }}
                    @else
                        <i class="fa-solid fa-graduation-cap text-[10px]"></i> {{ __('app.student_portal') }}
                    @endif
                </span>
            </div>

            {{-- Responsive Dynamic Title --}}
            <h2 id="portalNavTitle" class="font-heading font-black text-slate-900 dark:text-white text-xs sm:text-sm md:text-base lg:text-lg tracking-tight truncate max-w-[150px] xs:max-w-[190px] sm:max-w-xs md:max-w-sm lg:max-w-md xl:max-w-lg leading-tight">
                {{ $title ?? __('Dashboard Panel') }}
            </h2>
        </div>
    </div>

    <!-- Right: Quick Actions (System Logic) + Notifications + Theme + Language + User Menu -->
    <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">
        
        <!-- Role-Based Quick Actions based on System Logic -->
        @if($role === 'student')
            {{-- Large Desktop Full Action Pills (2xl screens only to prevent congestion) --}}
            <div class="hidden 2xl:flex items-center gap-2">
                <button type="button" 
                        onclick="window.openStudentPreviewGuide ? window.openStudentPreviewGuide('overview') : (window.openModal ? window.openModal('studentPreviewGuideModal') : null)" 
                        class="btn-lift px-3.5 py-2 bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-200 text-slate-950 text-xs font-black rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-950 text-[11px] animate-pulse"></i>
                    <span>{{ $isAr ? 'دليل الطالب' : 'Student Guide' }}</span>
                </button>
                <button type="button" 
                        onclick="window.openModal ? window.openModal('excuseModal') : document.getElementById('excuseModal')?.classList.remove('hidden')" 
                        class="btn-lift px-3.5 py-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 text-xs font-black rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-file-signature text-[11px]"></i>
                    <span>{{ __('app.portal.submit_excuse') }}</span>
                </button>
                <button type="button" 
                        onclick="window.openModal ? window.openModal('homeworkExceptionModal') : document.getElementById('homeworkExceptionModal')?.classList.remove('hidden')" 
                        class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-clipboard-question text-[11px]"></i>
                    <span>{{ __('app.portal.submit_exception') }}</span>
                </button>
            </div>

            {{-- Compact Quick Actions Dropdown (Visible on all screens < 2xl) --}}
            <div class="relative 2xl:hidden" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" 
                        type="button" 
                        class="h-9 sm:h-10 px-2.5 sm:px-3 rounded-xl bg-gradient-to-r from-teal-500/10 to-emerald-500/10 dark:from-teal-950/60 dark:to-emerald-950/60 border border-teal-500/30 text-teal-700 dark:text-teal-300 hover:bg-teal-500/20 text-xs font-bold flex items-center gap-1.5 transition-all shadow-2xs cursor-pointer active:scale-95"
                        title="{{ $isAr ? 'إجراءات سريعة' : 'Quick Actions' }}">
                    <i class="fa-solid fa-bolt text-amber-500 text-xs"></i>
                    <span class="hidden sm:inline font-heading">{{ $isAr ? 'إجراءات' : 'Actions' }}</span>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="{'rotate-180': open}"></i>
                </button>

                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                     x-cloak
                     style="display: none;"
                     class="absolute {{ $isAr ? 'left-0' : 'right-0' }} mt-2 w-56 sm:w-64 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xl p-1.5 z-50 space-y-1">
                    
                    <button type="button"
                            @click="open = false; window.openStudentPreviewGuide ? window.openStudentPreviewGuide('overview') : (window.openModal ? window.openModal('studentPreviewGuideModal') : null)"
                            class="w-full text-{{ $isAr ? 'right' : 'left' }} flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition-colors cursor-pointer border-b border-slate-100 dark:border-slate-800">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </span>
                        <div class="leading-tight">
                            <p class="font-bold">{{ $isAr ? 'دليل الطالب والشرح التفاعلي' : 'Student Guide & Live Demo' }}</p>
                            <span class="text-[10px] font-mono text-slate-400">{{ $isAr ? 'معاينة حية لجميع الأقسام' : 'Interactive full preview' }}</span>
                        </div>
                    </button>

                    <button type="button"
                            @click="open = false; window.openModal ? window.openModal('excuseModal') : document.getElementById('excuseModal')?.classList.remove('hidden')"
                            class="w-full text-{{ $isAr ? 'right' : 'left' }} flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition-colors cursor-pointer">
                        <span class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-file-signature"></i>
                        </span>
                        <div class="leading-tight">
                            <p class="font-bold">{{ __('app.portal.submit_excuse') }}</p>
                            <span class="text-[10px] font-mono text-slate-400">{{ $isAr ? 'طلب عذر غياب عن حصة' : 'Request session excuse' }}</span>
                        </div>
                    </button>

                    <button type="button"
                            @click="open = false; window.openModal ? window.openModal('homeworkExceptionModal') : document.getElementById('homeworkExceptionModal')?.classList.remove('hidden')"
                            class="w-full text-{{ $isAr ? 'right' : 'left' }} flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-700 dark:hover:text-teal-300 transition-colors cursor-pointer">
                        <span class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-clipboard-question"></i>
                        </span>
                        <div class="leading-tight">
                            <p class="font-bold">{{ __('app.portal.submit_exception') }}</p>
                            <span class="text-[10px] font-mono text-slate-400">{{ $isAr ? 'طلب استثناء واجب' : 'Request assignment extension' }}</span>
                        </div>
                    </button>
                </div>
            </div>

        @elseif($role === 'teacher')
            <div class="flex items-center gap-1.5 sm:gap-2">
                <button type="button" 
                        onclick="window.openTeacherPreviewModal ? window.openTeacherPreviewModal() : (window.openModal ? window.openModal('teacherPreviewGuideModal') : null)" 
                        class="btn-lift h-9 sm:h-10 px-2.5 sm:px-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-xs font-black rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all"
                        title="{{ $isAr ? 'دليل استخدام المنصة' : 'Platform Guide & Preview' }}">
                    <i class="fa-solid fa-wand-magic-sparkles text-xs animate-pulse"></i>
                    <span class="hidden sm:inline">{{ $isAr ? 'دليل المنصة' : 'Platform Guide' }}</span>
                </button>
                <button type="button" 
                        onclick="window.openCreateSessionModal ? window.openCreateSessionModal() : (window.openModal ? window.openModal('createSessionModal') : null)" 
                        class="btn-lift h-9 sm:h-10 px-2.5 sm:px-3.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span class="hidden sm:inline">{{ __('Schedule New Session') }}</span>
                    <span class="sm:hidden">{{ $isAr ? 'جلسة' : 'Session' }}</span>
                </button>
            </div>

        @elseif($role === 'parent')
            <div class="flex items-center gap-1.5 sm:gap-2">
                <button type="button" 
                        onclick="window.print()" 
                        class="btn-lift h-9 sm:h-10 px-2.5 sm:px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all hidden sm:flex"
                        title="{{ __('Print Academic Report') }}">
                    <i class="fa-solid fa-print"></i>
                    <span class="hidden md:inline">{{ __('Print Academic Report') }}</span>
                </button>
                <button type="button" 
                        onclick="window.openModal ? window.openModal('linkChildModal') : document.getElementById('linkChildModal')?.classList.remove('hidden')" 
                        class="btn-lift h-9 sm:h-10 px-3 sm:px-3.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span class="hidden sm:inline">{{ __('Link Child') }}</span>
                </button>
            </div>
        @endif

        <!-- Real-Time Notification Bell & Dropdown -->
        <div class="relative" 
             x-data="{
                 open: false,
                 unreadCount: 0,
                 recent: [],
                 init() {
                     window.addEventListener('notifications-updated', (e) => {
                         if (e.detail) {
                             this.unreadCount = e.detail.unread_count || 0;
                             if (e.detail.recent_notifications) {
                                 this.recent = e.detail.recent_notifications;
                             }
                         }
                     });
                     window.addEventListener('new-notification-received', (e) => {
                         if (e.detail) {
                             this.unreadCount++;
                             this.recent.unshift(e.detail);
                             if (this.recent.length > 8) this.recent.pop();
                         }
                     });
                 },
                 markRead(item) {
                     if (!item.is_read) {
                         item.is_read = true;
                         if (this.unreadCount > 0) this.unreadCount--;
                         if (window.markNotificationAsRead) {
                             window.markNotificationAsRead(item.id);
                         }
                     }
                     if (item.action_url) {
                         window.location.href = item.action_url;
                     }
                 },
                 markAll() {
                     this.unreadCount = 0;
                     this.recent.forEach(n => n.is_read = true);
                     if (window.markAllNotificationsAsRead) {
                         window.markAllNotificationsAsRead();
                     }
                 }
             }" 
             @click.away="open = false">
            
            <button @click="open = !open; if(open && window.pollNotifications) window.pollNotifications(true);" 
                    type="button" 
                    class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-teal-950/60 hover:text-teal-600 dark:hover:text-teal-400 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center transition-all shadow-2xs cursor-pointer active:scale-95"
                    title="{{ __('Notifications') }}"
                    aria-label="{{ __('Notifications') }}">
                <i class="fa-solid fa-bell text-sm sm:text-base"></i>
                
                {{-- Pulse Unread Badge --}}
                <span x-show="unreadCount > 0" 
                      x-transition
                      x-cloak
                      class="absolute -top-1 -end-1 min-w-[18px] h-[18px] px-1 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-mono font-extrabold text-[10px] rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900 shadow-xs animate-pulse">
                    <span x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                </span>
            </button>

            <!-- Notification Dropdown Menu (Full Responsive Clamping) -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 x-cloak
                 style="display: none;"
                 class="fixed sm:absolute {{ $isAr ? 'left-3 sm:left-0' : 'right-3 sm:right-0' }} top-16 sm:top-full mt-2 w-[calc(100vw-1.5rem)] sm:w-88 md:w-96 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xl overflow-hidden z-50 max-h-[85vh] flex flex-col">
                
                {{-- Header --}}
                <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-500 animate-ping"></span>
                        <h3 class="font-heading font-black text-xs sm:text-sm text-slate-900 dark:text-white">
                            {{ __('Notifications') }}
                        </h3>
                        <span x-show="unreadCount > 0" 
                              class="text-[10px] font-mono font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 px-2 py-0.5 rounded-full border border-rose-500/20"
                              x-text="unreadCount + ' {{ __('unread') }}'">
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button x-show="unreadCount > 0"
                                @click="markAll()" 
                                type="button"
                                class="px-2 py-1 text-[11px] font-mono font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors cursor-pointer flex items-center gap-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50"
                                title="{{ __('Mark all as read') }}">
                            <i class="fa-solid fa-check-double text-teal-600 dark:text-teal-400"></i>
                            <span class="text-[10px]">{{ __('Mark all read') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Notification List --}}
                <div class="overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 max-h-72 sm:max-h-80 custom-scrollbar">
                    <template x-if="recent.length === 0">
                        <div class="p-8 text-center space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl">
                                <i class="fa-solid fa-bell-slash"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                                {{ __('No notifications yet') }}
                            </p>
                        </div>
                    </template>

                    <template x-for="item in recent" :key="item.id">
                        <div @click="markRead(item)"
                             :class="{'bg-teal-50/40 dark:bg-teal-950/20': !item.is_read, 'hover:bg-slate-50 dark:hover:bg-slate-800/40': true}"
                             class="p-3 sm:p-3.5 transition-colors cursor-pointer flex items-start gap-3 relative group">
                            
                            {{-- Unread Dot --}}
                            <span x-show="!item.is_read" 
                                  class="absolute top-4 {{ $isAr ? 'left-3' : 'right-3' }} w-2 h-2 rounded-full bg-teal-500">
                            </span>

                            {{-- Type Icon --}}
                            <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center text-xs font-bold shadow-2xs"
                                 :class="{
                                     'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300': item.type === 'ASSIGNMENT_DEADLINE_REMINDER',
                                     'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300': item.type === 'ADMIN_APPROVAL_ALERT',
                                     'bg-teal-100 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300': item.type !== 'ASSIGNMENT_DEADLINE_REMINDER' && item.type !== 'ADMIN_APPROVAL_ALERT'
                                 }">
                                <template x-if="item.type === 'ASSIGNMENT_DEADLINE_REMINDER'">
                                    <i class="fa-solid fa-clock"></i>
                                </template>
                                <template x-if="item.type === 'ADMIN_APPROVAL_ALERT'">
                                    <i class="fa-solid fa-circle-check"></i>
                                </template>
                                <template x-if="item.type !== 'ASSIGNMENT_DEADLINE_REMINDER' && item.type !== 'ADMIN_APPROVAL_ALERT'">
                                    <i class="fa-solid fa-bell"></i>
                                </template>
                            </div>

                            <div class="flex-1 min-w-0 {{ $isAr ? 'pl-3' : 'pr-3' }}">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="item.title"></h4>
                                </div>
                                <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-snug line-clamp-2" x-text="item.body"></p>
                                <span class="text-[10px] font-mono text-slate-400 mt-1 inline-block" x-text="item.created_at ? (new Date(item.created_at)).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '{{ __('Just now') }}'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Footer Link --}}
                <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 text-center shrink-0">
                    <a href="{{ route('student-portal') }}#notifications" 
                       @click="open = false"
                       class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 hover:underline inline-flex items-center gap-1">
                        <span>{{ __('View all notifications') }}</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Language Switcher -->
        <a href="{{ route('lang.switch', $otherLocale) }}" 
           class="h-9 sm:h-10 px-2 sm:px-3 rounded-xl text-xs font-mono font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-teal-950/60 hover:text-teal-600 dark:hover:text-teal-400 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center gap-1 transition-all shadow-2xs shrink-0" 
           title="Switch Language">
            <i class="fa-solid fa-globe text-xs"></i> 
            <span class="font-bold">{{ $isAr ? 'EN' : 'عربي' }}</span>
        </a>

        {{-- Dark / Light Mode Toggle --}}
        <button type="button" 
                onclick="window.EliteTheme ? window.EliteTheme.toggle() : (window.togglePortalTheme ? window.togglePortalTheme() : null)"
                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-500 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0 active:scale-95"
                title="{{ __('Toggle Dark Mode') }}" 
                aria-label="{{ __('Toggle Dark Mode') }}">
            <i class="theme-toggle-icon fa-solid fa-moon text-sm sm:text-base"></i>
        </button>

        <!-- User Profile Pill & Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" 
                    type="button" 
                    class="h-9 sm:h-10 flex items-center gap-2 p-1 sm:px-2.5 sm:py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/80 hover:border-teal-500/50 transition-all shadow-2xs cursor-pointer shrink-0 active:scale-95">
                <div class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-lg bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xs flex items-center justify-center shadow-xs shrink-0">
                    {{ mb_substr($user->name ?? 'U', 0, 1) }}
                </div>
                <div class="hidden lg:block text-{{ $isAr ? 'right' : 'left' }} leading-tight">
                    <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate max-w-[110px]">{{ $user->name ?? 'User' }}</p>
                    <span class="text-[10px] font-mono font-bold text-teal-600 dark:text-teal-400 capitalize">{{ $role }}</span>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400 hidden sm:inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 x-cloak
                 style="display: none;"
                 class="absolute {{ $isAr ? 'left-0' : 'right-0' }} mt-2 w-56 sm:w-60 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xl py-1.5 z-50">
                
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800/80">
                    <p class="text-xs font-black text-slate-900 dark:text-white truncate">{{ $user->name ?? 'User' }}</p>
                    <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ $user->email ?? '' }}</p>
                    <div class="mt-1.5 inline-block px-2 py-0.5 bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-mono text-[9px] font-extrabold rounded-md uppercase border border-teal-200/60 dark:border-teal-800/60">
                        {{ $role }}
                    </div>
                </div>

                @if($user && $user->isAdmin())
                    <a href="/admin" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition-colors">
                        <i class="fa-solid fa-bolt text-xs"></i> 
                        <span>{{ __('Admin Panel (Filament)') }}</span>
                    </a>
                @endif

                @if($role === 'student')
                    <a href="{{ route('student.profile') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-600 transition-colors">
                        <i class="fa-solid fa-user-gear text-xs"></i> 
                        <span>{{ $isAr ? 'الملف الشخصي والإعدادات' : 'Profile & Settings' }}</span>
                    </a>
                @endif

                <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-600 transition-colors">
                    <i class="fa-solid fa-house text-xs"></i> 
                    <span>{{ __('navbar.home') }}</span>
                </a>

                <div class="border-t border-slate-100 dark:border-slate-800/80 mt-1 pt-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-{{ $isAr ? 'right' : 'left' }} flex items-center gap-2 px-4 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i> 
                            <span>{{ __('navbar.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
