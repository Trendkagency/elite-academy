@if(demo_enabled())
    <div class="demo-admin-login-hook mb-6 space-y-4">
        @if(config('demo.guards.show_banner', true))
            <div class="p-3 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-800 dark:text-amber-300 text-xs font-semibold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ config('demo.guards.banner_text', 'Demo mode — data resets automatically.') }}</span>
            </div>
        @endif

        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 shadow-sm space-y-3">
            <x-demo.credentials />
        </div>

        <div class="relative my-3 text-center">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="bg-white dark:bg-slate-900 px-3 text-slate-400 font-semibold">
                    {{ app()->getLocale() === 'ar' ? 'أو كتابة بيانات الدخول يدوياً' : 'Or Manual Admin Credentials' }}
                </span>
            </div>
        </div>
    </div>
@endif
