@php
    $user = auth()->user();
    $avatarUrl = $user?->getFilamentAvatarUrl();
    $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : collect();
    if ($roles->isEmpty()) {
        $roles = collect([$user?->isAdmin() ? 'Administrator' : 'User']);
    }
@endphp

<div class="ec-profile-hero relative overflow-hidden rounded-2xl border p-6 md:p-8 backdrop-blur-xl transition-all duration-300"
     style="background: var(--sg-surface); border-color: var(--sg-border); box-shadow: var(--sg-shadow);">
    <!-- Specular horizon glow -->
    <div class="pointer-events-none absolute top-0 left-6 right-6 h-[1.5px]"
         style="background: linear-gradient(90deg, transparent, rgba(var(--sys-primary-rgb), 0.8), rgba(var(--sys-accent-rgb), 0.6), transparent);"></div>

    <!-- Ambient circular glow behind avatar -->
    <div class="pointer-events-none absolute -top-16 -right-16 h-56 w-56 rounded-full blur-3xl opacity-25"
         style="background: radial-gradient(circle, var(--sys-primary) 0%, transparent 70%);"></div>

    <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6">
        <!-- Avatar Preview with glowing ring -->
        <div class="relative shrink-0">
            <div class="relative h-28 w-28 md:h-32 md:w-32 rounded-full ring-4 shadow-2xl overflow-hidden flex items-center justify-center"
                 style="ring-color: rgba(var(--sys-primary-rgb), 0.35); background: linear-gradient(135deg, rgba(var(--sys-primary-rgb), 0.25) 0%, rgba(var(--sys-accent-rgb), 0.25) 100%);">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                @else
                    <span class="text-4xl font-black text-white" style="font-family: 'Cairo', sans-serif;">
                        {{ mb_strtoupper(mb_substr($user->name ?? 'Admin', 0, 2)) }}
                    </span>
                @endif
            </div>
            <!-- Online / Active Status Badge -->
            <span class="absolute bottom-1 right-1 h-5 w-5 rounded-full bg-emerald-500 ring-4 ring-slate-900 shadow-lg"
                  title="{{ __('Active Account / حساب نشط') }}"></span>
        </div>

        <!-- Details -->
        <div class="flex-1 text-center md:text-start space-y-2">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5">
                <h2 class="text-2xl md:text-3xl font-black tracking-tight" style="color: var(--sg-text); font-family: 'Cairo', sans-serif;">
                    {{ $user->name }}
                </h2>
                @if ($user->isSuperAdmin())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black shadow-md text-white"
                          style="background: linear-gradient(135deg, var(--sys-primary) 0%, var(--sys-primary-dark) 100%); box-shadow: 0 4px 12px -2px rgba(var(--sys-primary-rgb), 0.5);">
                        <i class="fa-solid fa-shield-halved text-[11px]"></i>
                        <span>{{ __('Super Admin / مدير عام') }}</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black shadow-md text-white bg-teal-600">
                        <i class="fa-solid fa-user-shield text-[11px]"></i>
                        <span>{{ __('Administrator / مسؤول') }}</span>
                    </span>
                @endif
            </div>

            <p class="text-sm font-semibold" style="color: var(--sg-text-dim);">
                {{ $user->job_title ?: ($user->isSuperAdmin() ? __('System Administrator & Engineering / إدارة وتطوير المنظومة') : __('Administrative User / مستخدم إداري')) }}
            </p>

            @if ($user->bio)
                <p class="text-xs md:text-sm max-w-2xl leading-relaxed" style="color: var(--sg-text-muted);">
                    {{ $user->bio }}
                </p>
            @endif

            <div class="pt-2 flex flex-wrap items-center justify-center md:justify-start gap-4 text-xs font-semibold" style="color: var(--sg-text-dim);">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-regular fa-envelope text-teal-400"></i>
                    <span>{{ $user->email }}</span>
                </span>
                @if ($user->phone)
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                        <span>{{ $user->phone }}</span>
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar-check text-sky-400"></i>
                    <span>{{ __('Member Since') }}: {{ $user->created_at?->format('M d, Y') }}</span>
                </span>
            </div>
        </div>
    </div>
</div>
