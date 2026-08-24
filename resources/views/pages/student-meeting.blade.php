@extends('layouts.app')

@section('content')
<section class="py-8 md:py-12 bg-slate-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.portal'), 'route' => 'student-portal'],
                ['label' => $session->title ?: (app()->getLocale() === 'ar' ? 'البث المباشر' : 'Live Meeting')],
            ]
        ])

        <div class="flex items-center justify-between">
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-white tracking-tight">
                {{ $session->title ?: (app()->getLocale() === 'ar' ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Stream Session') }}
            </h1>
            <a href="{{ route('student-portal') }}" class="btn-lift px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold font-mono transition-all">
                ← {{ app()->getLocale() === 'ar' ? 'العودة للمنصة' : 'Back to Dashboard' }}
            </a>
        </div>

        {{-- Embedded Meeting Container Component --}}
        @include('components.meeting-container', ['session' => $session, 'user' => $user])
    </div>
</section>
@endsection
