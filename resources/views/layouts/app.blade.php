<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth h-full bg-[#FAFAF9] text-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0F172A">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>{{ $pageTitle ?? config('app.name') }}</title>
    @isset($pageDescription)
        <meta name="description" content="{{ $pageDescription }}">
    @endisset
    <style>
        /* Critical styles to eliminate white flash during back/forward navigation and hydration */
        html, body {
            background-color: #FAFAF9 !important;
            color: #0F172A;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('dist/output.css') }}?v={{ time() }}">
    @stack('head')
</head>
<body class="font-sans antialiased overflow-x-hidden bg-[#FAFAF9] text-slate-900 selection:bg-teal-100 selection:text-teal-900 flex flex-col min-h-screen m-0 p-0">

    {{-- Scroll Progress Bar --}}
    @unless(request()->boolean('iframe'))
        <div id="scroll-progress" class="fixed top-0 left-0 right-0 h-1 bg-teal-500 z-[60]" style="width: 0%"></div>
    @endunless

    @unless(($minimalLayout ?? false) || request()->boolean('iframe'))
        @include('partials.ambient')
        @include('partials.navbar')
    @endunless

    <main class="flex-grow w-full bg-[#FAFAF9] min-h-[60vh]">
        @yield('content')
    </main>

    @unless(($minimalLayout ?? false) || request()->boolean('iframe'))
        @include('partials.footer')
    @endunless

    {{-- Back to Top Button --}}
    @unless(request()->boolean('iframe'))
        <button id="back-to-top" aria-label="Back to top">↑</button>
    @endunless

    <script src="{{ asset('js/scroll-reveal.js') }}"></script>
    @stack('scripts')
</body>
</html>
