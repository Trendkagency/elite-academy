@php
    $user = auth()->user();
    $role = $user ? $user->getRoleName() : 'student';
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $otherLocale = $isAr ? 'en' : 'ar';
    $otherLocaleName = $isAr ? 'English (EN)' : 'العربية (AR)';
@endphp

<!-- Master Portal Navbar -->
<header class="sticky top-0 z-30 h-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between shadow-xs transition-colors">
    
    <!-- Left: Hamburger Toggle (Mobile) + Breadcrumb / Portal Title -->
    <div class="flex items-center gap-4">
        <button type="button" onclick="togglePortalSidebar(true)" class="portal-navbar-toggle p-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-teal-950/50 hover:text-teal-600 transition-colors shadow-xs cursor-pointer" aria-label="Open Sidebar">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="hidden sm:flex flex-col">
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-mono">
                <a href="{{ route('home') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">{{ __('navbar.home') }}</a>
                <span>/</span>
                <span class="text-teal-600 dark:text-teal-400 font-bold">
                    @if($role === 'teacher')
                        {{ __('app.teacher_portal') }}
                    @elseif($role === 'parent')
                        {{ __('app.parent_portal') }}
                    @else
                        {{ __('app.student_portal') }}
                    @endif
                </span>
            </div>
            <h2 id="portalNavTitle" class="font-heading font-black text-slate-900 dark:text-white text-lg tracking-tight">
                {{ $title ?? __('Dashboard Panel') }}
            </h2>
        </div>
    </div>

    <!-- Right: Quick Actions, Theme, Language, Notifications, User Menu -->
    <div class="flex items-center gap-2 sm:gap-3">
        
        <!-- Role Quick Action Buttons -->
        @if($role === 'student')
            <div class="hidden md:flex items-center gap-2">
                <button onclick="document.getElementById('excuseModal')?.classList.remove('hidden')" class="btn-lift px-3.5 py-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span><i class="fa-solid fa-file-lines"></i></span> {{ __('app.portal.submit_excuse') }}
                </button>
                <button onclick="document.getElementById('homeworkExceptionModal')?.classList.remove('hidden')" class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span><i class="fa-solid fa-clipboard-list"></i></span> {{ __('app.portal.submit_exception') }}
                </button>
            </div>
        @elseif($role === 'teacher')
            <div class="hidden md:flex items-center gap-2">
                <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span><i class="fa-solid fa-plus"></i></span> {{ __('Schedule New Session') }}
                </button>
            </div>
        @elseif($role === 'parent')
            <div class="hidden md:flex items-center gap-2">
                <button type="button" onclick="window.print()" class="btn-lift px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span><i class="fa-solid fa-print"></i></span> {{ __('Print Academic Report') }}
                </button>
                <button type="button" onclick="document.getElementById('linkChildModal')?.classList.remove('hidden')" class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span><i class="fa-solid fa-plus"></i></span> {{ __('Link Child') }}
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
                    class="relative p-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-teal-950/60 hover:text-teal-600 transition-colors shadow-xs cursor-pointer"
                    title="{{ __('Notifications') }}"
                    aria-label="{{ __('Notifications') }}">
                <i class="fa-solid fa-bell text-base"></i>
                
                {{-- Pulse Unread Badge --}}
                <span x-show="unreadCount > 0" 
                      x-transition
                      x-cloak
                      class="absolute -top-1 -end-1 min-w-[20px] h-5 px-1 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-mono font-extrabold text-[10px] rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900 shadow-xs animate-pulse">
                    <span x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                </span>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 x-cloak
                 style="display: none;"
                 class="absolute {{ $isAr ? 'left-0 sm:-left-12' : 'right-0 sm:-right-12' }} mt-2 w-80 sm:w-96 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xl overflow-hidden z-50">
                
                {{-- Header --}}
                <div class="px-4 py-3.5 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
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
                        {{-- ⚡ Instant Test Button --}}
                        <button type="button" 
                                onclick="window.triggerTestPush(this)"
                                class="px-2.5 py-1 text-[11px] font-bold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/60 hover:bg-teal-100 dark:hover:bg-teal-900/80 rounded-xl border border-teal-200 dark:border-teal-800 transition-colors cursor-pointer flex items-center gap-1 shadow-2xs"
                                title="{{ __('Test Real-Time Alert') }}">
                            <i class="fa-solid fa-bolt text-amber-500"></i>
                            <span class="hidden sm:inline">{{ __('Test Push') }}</span>
                        </button>

                        {{-- Mark All Read --}}
                        <button x-show="unreadCount > 0"
                                @click="markAll()" 
                                type="button"
                                class="px-2 py-1 text-[11px] font-mono font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors cursor-pointer"
                                title="{{ __('Mark all as read') }}">
                            <i class="fa-solid fa-check-double"></i>
                        </button>
                    </div>
                </div>

                {{-- Notification List --}}
                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
                    <template x-if="recent.length === 0">
                        <div class="p-8 text-center space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl">
                                <i class="fa-solid fa-bell-slash"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                                {{ __('No notifications yet') }}
                            </p>
                            <p class="text-[11px] font-mono text-slate-400">
                                {{ __('Click "Test Push" above to test live alerts!') }}
                            </p>
                        </div>
                    </template>

                    <template x-for="item in recent" :key="item.id">
                        <div @click="markRead(item)"
                             :class="{'bg-teal-50/40 dark:bg-teal-950/20': !item.is_read, 'hover:bg-slate-50 dark:hover:bg-slate-800/40': true}"
                             class="p-3.5 transition-colors cursor-pointer flex items-start gap-3 relative group">
                            
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
                <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-100 dark:border-slate-800 text-center">
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
        <a href="{{ route('lang.switch', $otherLocale) }}" class="px-3 py-2 rounded-xl text-xs font-mono font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-teal-950/60 hover:text-teal-600 transition-colors shadow-xs" title="Switch Language">
            <i class="fa-solid fa-globe"></i> {{ $isAr ? 'EN' : 'عربي' }}
        </a>

        <!-- User Profile Pill & Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" type="button" class="flex items-center gap-2.5 p-1.5 sm:px-3 sm:py-1.5 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 hover:border-teal-500/50 transition-all shadow-xs cursor-pointer">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xs flex items-center justify-center shadow-xs">
                    {{ mb_substr($user->name ?? 'U', 0, 1) }}
                </div>
                <div class="hidden sm:block text-{{ $isAr ? 'right' : 'left' }} leading-tight">
                    <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate max-w-[120px]">{{ $user->name ?? 'User' }}</p>
                    <span class="text-[10px] font-mono font-bold text-teal-600 dark:text-teal-400 capitalize">{{ $role }}</span>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="display: none;"
                 class="absolute {{ $isAr ? 'left-0' : 'right-0' }} mt-2 w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-2 z-50">
                
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-xs font-black text-slate-900 dark:text-white">{{ $user->name }}</p>
                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                </div>

                @if($user && $user->isAdmin())
                    <a href="/admin" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition-colors">
                        <span><i class="fa-solid fa-bolt"></i></span> {{ __('Admin Panel (Filament)') }}
                    </a>
                @endif

                <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-600 transition-colors">
                    <span><i class="fa-solid fa-house"></i></span> {{ __('navbar.home') }}
                </a>

                <div class="border-t border-slate-100 dark:border-slate-800 mt-1 pt-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-{{ $isAr ? 'right' : 'left' }} flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer">
                            <span><i class="fa-solid fa-arrow-right-from-bracket"></i></span> {{ __('navbar.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
