@php
    $user = auth()->user();
    $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : collect();
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-2">
    <!-- Stat 1: Account Status -->
    <div class="rounded-xl p-4 border" style="background: rgba(var(--sys-primary-rgb), 0.05); border-color: var(--sg-border);">
        <div class="text-xs font-bold" style="color: var(--sg-text-dim);">{{ __('Account Status / حالة الحساب') }}</div>
        <div class="mt-1 flex items-center gap-2">
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-base font-black text-emerald-500 uppercase">{{ $user->status instanceof \App\Enums\AccountStatus ? $user->status->value : (string)$user->status }}</span>
        </div>
    </div>

    <!-- Stat 2: User ID -->
    <div class="rounded-xl p-4 border" style="background: rgba(var(--sys-primary-rgb), 0.05); border-color: var(--sg-border);">
        <div class="text-xs font-bold" style="color: var(--sg-text-dim);">{{ __('System User ID / المعرّف الرقمي') }}</div>
        <div class="mt-1 text-base font-black" style="color: var(--sg-text);">#{{ $user->id }}</div>
    </div>

    <!-- Stat 3: Assigned Roles -->
    <div class="rounded-xl p-4 border" style="background: rgba(var(--sys-primary-rgb), 0.05); border-color: var(--sg-border);">
        <div class="text-xs font-bold" style="color: var(--sg-text-dim);">{{ __('Assigned Roles / الصلاحيات') }}</div>
        <div class="mt-1 flex flex-wrap gap-1">
            @forelse ($roles as $role)
                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold text-white shadow-sm" style="background: var(--sys-primary);">{{ $role }}</span>
            @empty
                <span class="text-sm font-semibold" style="color: var(--sg-text);">{{ $user->isAdmin() ? 'Administrator' : 'User' }}</span>
            @endforelse
        </div>
    </div>

    <!-- Stat 4: Account Security -->
    <div class="rounded-xl p-4 border" style="background: rgba(var(--sys-primary-rgb), 0.05); border-color: var(--sg-border);">
        <div class="text-xs font-bold" style="color: var(--sg-text-dim);">{{ __('Last Updated / آخر نشاط') }}</div>
        <div class="mt-1 text-base font-black" style="color: var(--sg-text);">{{ $user->updated_at?->diffForHumans() ?? 'Recently' }}</div>
    </div>
</div>
