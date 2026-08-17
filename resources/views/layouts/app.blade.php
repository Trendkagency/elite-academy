<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? config('app.name') }}</title>
    @isset($pageDescription)
        <meta name="description" content="{{ $pageDescription }}">
    @endisset
    <link rel="stylesheet" href="{{ asset('dist/output.css') }}?v={{ time() }}">
    @stack('head')
</head>
<body @class([
    'font-sans antialiased overflow-x-hidden',
    'bg-[#FAFAF9] text-slate-900 selection:bg-teal-100 selection:text-teal-900' => ! ($minimalLayout ?? false),
    'bg-[#FAFAF9] text-slate-800 selection:bg-teal-500 selection:text-white flex flex-col min-h-screen' => ($minimalLayout ?? false),
])>

    {{-- Scroll Progress Bar --}}
    <div id="scroll-progress" style="width: 0%"></div>

    @unless($minimalLayout ?? false)
        @include('partials.ambient')
    @endunless

    @unless($minimalLayout ?? false)
        @include('partials.navbar')
    @endunless

    <main>
        @yield('content')
    </main>

    @unless($minimalLayout ?? false)
        @include('partials.footer')
    @endunless

    {{-- Back to Top Button --}}
    <button id="back-to-top" aria-label="Back to top">↑</button>

    <script src="{{ asset('js/scroll-reveal.js') }}"></script>
    @stack('scripts')
</body>
</html>
