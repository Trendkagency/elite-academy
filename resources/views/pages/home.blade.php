@extends('layouts.app')

@section('content')
@php
    $siteJsonLd = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "@id" => url('/') . "/#breadcrumb",
        "itemListElement" => [
            [
                "@type" => "ListItem",
                "position" => 1,
                "name" => app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home',
                "item" => url('/')
            ]
        ]
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($siteJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
    @php
        $layoutRaw = \App\Models\SiteSetting::get('sections_layout');
        $layout = $layoutRaw ? json_decode($layoutRaw, true) : null;

        $sectionsMap = [
            'hero-slider' => 'pages.home.hero-slider',
            'stats-overlay' => 'pages.home.stats-overlay',
            'why-choose' => 'pages.home.why-choose',
            'about-preview' => 'pages.home.about-preview',
            'subjects-grid' => 'pages.home.subjects-grid',
            'teachers-marquee' => 'pages.home.teachers-marquee',
            'testimonials' => 'pages.home.testimonials',
            'faq-section' => 'pages.home.faq-section',
            'cta_section' => 'pages.home.cta-section',
        ];
    @endphp

    @if(is_array($layout) && count($layout) > 0)
        @foreach($layout as $sec)
            @if(($sec['is_enabled'] ?? true) && isset($sectionsMap[$sec['key']]))
                @include($sectionsMap[$sec['key']])
            @endif
        @endforeach
    @else
        {{-- Full Original Landing Page --}}
        @include('pages.home.hero-slider')
        @include('pages.home.stats-overlay')
        @include('pages.home.why-choose')
        @include('pages.home.about-preview')
        @include('pages.home.subjects-grid')
        @include('pages.home.teachers-marquee')
        @include('pages.home.testimonials')
        @include('pages.home.faq-section')
        @include('pages.home.cta-section')
    @endif
@endsection
