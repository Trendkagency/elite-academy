@php
    $user = auth()->user();
    $role = $user ? $user->getRoleName() : 'student';
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';

    $roleBadgeText = match($role) {
        'teacher' => __('app.teacher_portal'),
        'parent'  => __('app.parent_portal'),
        'admin'   => __('app.admin_portal'),
        default   => __('app.student_portal'),
    };

    $roleIcon = match($role) {
        'teacher' => '👨‍🏫',
        'parent'  => '👨‍👩‍👧',
        'admin'   => '⚡',
        default   => '🎓',
    };
@endphp

<!-- Sidebar Backdrop for Mobile -->
<div id="portalSidebarBackdrop" onclick="togglePortalSidebar(false)" class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-xs lg:hidden transition-opacity opacity-0 pointer-events-none"></div>

<!-- Master Portal Sidebar -->
<aside id="portalSidebar" class="portal-sidebar-wrapper bg-slate-900 border-{{ $isAr ? 'l' : 'r' }} border-slate-800/80 text-slate-100 flex flex-col shadow-2xl">
    
    <!-- Sidebar Header: Brand & Portal Badge -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/80 bg-slate-950/40">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 p-0.5 shadow-lg shadow-teal-500/20 group-hover:scale-105 transition-transform flex items-center justify-center">
                <span class="text-slate-950 font-black font-heading text-lg">E</span>
            </div>
            <div>
                <span class="font-heading font-black text-xl text-white tracking-tight flex items-center gap-1">
                    <span class="text-teal-400">Elite</span> Academy<span class="text-teal-500">.</span>
                </span>
                <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold uppercase tracking-wider text-teal-400 bg-teal-950/80 px-2 py-0.5 rounded-full border border-teal-800/60">
                    <span>{{ $roleIcon }}</span> {{ $roleBadgeText }}
                </span>
            </div>
        </a>

        <!-- Mobile Close Button -->
        <button type="button" onclick="togglePortalSidebar(false)" class="portal-sidebar-close-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer" aria-label="Close Sidebar">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation Links Container -->
    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6 scrollbar-thin scrollbar-thumb-slate-800">
        
        <!-- Role Specific Main Nav Group -->
        <div class="space-y-1">
            <span class="px-3 text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider block mb-2">
                {{ __('Navigation Menu') }}
            </span>

            @if($role === 'student')
                <!-- Student Navigation -->
                <a href="#overview" onclick="switchPortalSection('overview')" class="portal-nav-item active flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📊</span>
                    <span>{{ __('app.portal.welcome_back') }} & {{ __('Overview') }}</span>
                </a>

                <a href="#liveSessions" onclick="switchPortalSection('liveSessions')" class="portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">🎥</span>
                    <span>{{ __('app.portal.upcoming_sessions') }}</span>
                </a>

                <a href="#assignments" onclick="switchPortalSection('assignments')" class="portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📝</span>
                    <span>{{ __('Assignments & Tests') }}</span>
                </a>

                <a href="#packages" onclick="switchPortalSection('packages')" class="portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">💳</span>
                    <span>{{ __('app.portal.current_package') }} & {{ __('Credits') }}</span>
                </a>

                <a href="#exceptions" onclick="switchPortalSection('exceptions')" class="portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📜</span>
                    <span>{{ __('Excuses & Exceptions') }}</span>
                </a>

            @elseif($role === 'teacher')
                <!-- Teacher Navigation -->
                <a href="#overview" onclick="switchTeacherTab('overview')" class="teacher-tab-btn active portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-tab="overview">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📊</span>
                    <span>{{ __('Faculty Overview') }}</span>
                </a>

                <a href="#sessions" onclick="switchTeacherTab('sessions')" class="teacher-tab-btn portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-tab="sessions">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📅</span>
                    <span>{{ __('Teaching Schedule & Rooms') }}</span>
                </a>

                <a href="#assignments" onclick="switchTeacherTab('assignments')" class="teacher-tab-btn portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-tab="assignments">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📝</span>
                    <span>{{ __('Assignments & Quizzes') }}</span>
                </a>

                <a href="#attendance" onclick="switchTeacherTab('attendance')" class="teacher-tab-btn portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-tab="attendance">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📋</span>
                    <span>{{ __('Student Attendance Sheets') }}</span>
                </a>

                <a href="#students" onclick="switchTeacherTab('students')" class="teacher-tab-btn portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-tab="students">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">🎓</span>
                    <span>{{ __('My Students') }}</span>
                </a>

            @elseif($role === 'parent')
                <!-- Parent Navigation -->
                <a href="#section-children" onclick="switchParentSection('children')" class="portal-nav-item active flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-section="children">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">👨‍👩‍👧</span>
                    <span>{{ __('Your Linked Children') }}</span>
                </a>

                <a href="#section-progress" onclick="switchParentSection('progress')" class="portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-section="progress">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📈</span>
                    <span>{{ __('Academic Performance & Grades') }}</span>
                </a>

                <a href="#section-courses" onclick="switchParentSection('courses')" class="portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-section="courses">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📚</span>
                    <span>{{ __('Enrolled Courses & Curriculum') }}</span>
                </a>

                <a href="#section-assignments" onclick="switchParentSection('assignments')" class="portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-section="assignments">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">📝</span>
                    <span>{{ __('Graded Assignments & Feedback') }}</span>
                </a>

                <a href="#section-attendance" onclick="switchParentSection('attendance')" class="portal-nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-teal-950/60 hover:text-teal-300 text-slate-200" data-section="attendance">
                    <span class="text-lg text-teal-400 group-hover:scale-110 transition-transform">🕒</span>
                    <span>{{ __('Attendance & Session Records') }}</span>
                </a>

                <button type="button" onclick="openLinkChildModal()" class="w-full text-{{ $isAr ? 'right' : 'left' }} flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all group hover:bg-amber-950/60 hover:text-amber-300 text-amber-400 cursor-pointer">
                    <span class="text-lg group-hover:scale-110 transition-transform">➕</span>
                    <span>{{ __('Link New Child Account') }}</span>
                </button>
            @endif
        </div>

        <!-- Quick Links & External Navigation -->
        <div class="space-y-1 pt-4 border-t border-slate-800/80">
            <span class="px-3 text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider block mb-2">
                {{ __('Platform Services') }}
            </span>

            @if($user && $user->isAdmin())
                <a href="/admin" class="flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-bold text-amber-400 hover:bg-amber-950/40 hover:text-amber-300 transition-all">
                    <span class="text-base">⚡</span>
                    <span>{{ __('Admin Panel (Filament)') }}</span>
                </a>
            @endif

            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-bold text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                <span class="text-base">🏠</span>
                <span>{{ __('navbar.home') }}</span>
            </a>

            <a href="{{ route('courses') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-bold text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                <span class="text-base">📚</span>
                <span>{{ __('navbar.courses') }}</span>
            </a>
        </div>
    </div>

    <!-- Bottom User Profile Card -->
    <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
        <div class="flex items-center justify-between gap-3 p-2 rounded-2xl bg-slate-900/90 border border-slate-800">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-sm flex items-center justify-center shrink-0 shadow-sm border border-teal-300/40">
                    {{ mb_substr($user->name ?? 'U', 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-extrabold text-white truncate">{{ $user->name ?? 'User' }}</p>
                    <p class="text-[10px] font-mono text-teal-400 truncate">{{ $user->email ?? '' }}</p>
                </div>
            </div>

            <!-- Logout Form Button -->
            <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" title="{{ __('navbar.logout') }}" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-950/40 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<style>
.portal-nav-item.active {
    background: linear-gradient(135deg, rgba(13, 148, 136, 0.25), rgba(16, 185, 129, 0.15)) !important;
    border: 1px solid rgba(13, 148, 136, 0.4) !important;
    color: #5EEAD4 !important;
    box-shadow: 0 4px 14px rgba(13, 148, 136, 0.15);
}
</style>
