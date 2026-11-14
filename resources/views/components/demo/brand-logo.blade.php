@props([
    'theme' => 'auto',
    'size' => 'md',
    'showPill' => true,
])

@php
    $customLogo = function_exists('brand_custom_logo_url') ? brand_custom_logo_url($theme) : null;
    $appName = function_exists('brand') ? brand('app_name', config('demo.branding.app_name') ?: env('DEMO_APP_NAME', 'EDU')) : (config('demo.branding.app_name') ?: env('DEMO_APP_NAME', 'EDU'));
    $tagline = function_exists('brand') ? brand('tagline', 'LMS Platform') : 'LMS Platform';
    $primaryColor = function_exists('brand_color') ? brand_color('primary', '#2563eb') : '#2563eb';
    $secondaryColor = function_exists('brand_color') ? brand_color('secondary', '#4f46e5') : '#4f46e5';

    $sizeClasses = match($size) {
        'sm' => [
            'icon' => 'w-8 h-8 rounded-lg',
            'img' => 'h-8 max-h-8',
            'svg' => 'w-4 h-4',
            'text' => 'text-lg',
            'pill' => 'text-[8px] px-1 py-0.5',
            'sub' => 'hidden',
        ],
        'lg' => [
            'icon' => 'w-12 h-12 rounded-2xl',
            'img' => 'h-12 sm:h-14 max-h-14',
            'svg' => 'w-7 h-7',
            'text' => 'text-2xl sm:text-3xl',
            'pill' => 'text-[10px] px-2 py-0.5',
            'sub' => 'text-xs',
        ],
        default => [
            'icon' => 'w-10 h-10 rounded-xl',
            'img' => 'h-10 sm:h-11 max-h-11',
            'svg' => 'w-5 h-5',
            'text' => 'text-xl sm:text-2xl',
            'pill' => 'text-[9px] px-1.5 py-0.5',
            'sub' => 'text-[10px]',
        ],
    };

    $textColor = match($theme) {
        'dark' => 'text-white',
        'light' => 'text-slate-900',
        default => 'text-slate-900 dark:text-white',
    };
@endphp

<div class="demo-brand-logo inline-flex items-center gap-2.5 select-none transition-transform duration-300 group-hover:scale-105">
    @if ($customLogo)
        {{-- Custom Uploaded System Logo --}}
        <div class="flex items-center gap-2">
            <img src="{{ $customLogo }}" alt="{{ $appName }}" class="{{ $sizeClasses['img'] }} w-auto object-contain">
            @if($showPill && function_exists('demo_enabled') && demo_enabled())
                <span class="{{ $sizeClasses['pill'] }} font-black uppercase tracking-wider rounded bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-300/70 dark:border-amber-700/60 leading-none">
                    DEMO
                </span>
            @endif
        </div>
    @else
        {{-- Dynamic System Colors Vector Emblem --}}
        <div class="{{ $sizeClasses['icon'] }} flex items-center justify-center text-white shadow-md shadow-teal-500/20 shrink-0"
             style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);">
            <svg class="{{ $sizeClasses['svg'] }} text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
        </div>

        {{-- Dynamic App Name & Demo Pill --}}
        <div class="flex flex-col text-start">
            <div class="flex items-center gap-2">
                <span class="font-heading font-black {{ $sizeClasses['text'] }} {{ $textColor }} tracking-tight leading-none">
                    {{ $appName }}
                </span>
                @if($showPill && function_exists('demo_enabled') && demo_enabled())
                    <span class="{{ $sizeClasses['pill'] }} font-black uppercase tracking-wider rounded bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-300/70 dark:border-amber-700/60 leading-none">
                        DEMO
                    </span>
                @endif
            </div>
            <span class="{{ $sizeClasses['sub'] }} font-semibold text-slate-400 dark:text-slate-500 leading-none mt-1">
                {{ $tagline }}
            </span>
        </div>
    @endif
</div>
