@extends('layouts.app')

@section('content')
@php
    $siteJsonLd = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "WebSite",
                "@id" => url('/') . "/#website",
                "url" => url('/'),
                "name" => "Elite Academy LMS",
                "description" => "Leading K-12 Interactive Learning Management System & Online Tutoring Platform in Egypt",
                "publisher" => [
                    "@id" => url('/') . "/#organization"
                ],
                "inLanguage" => app()->getLocale(),
                "potentialAction" => [
                    "@type" => "SearchAction",
                    "target" => url('/courses') . "?search={search_term_string}",
                    "query-input" => "required name=search_term_string"
                ]
            ],
            [
                "@type" => "EducationalOrganization",
                "@id" => url('/') . "/#organization",
                "name" => "Elite Academy LMS",
                "alternateName" => "أكاديمية إيليت التعليمية",
                "url" => url('/'),
                "logo" => asset('images/logo_500.webp'),
                "image" => asset('images/academy_campus.webp'),
                "description" => "Ministry-accredited interactive K-12 educational platform providing live classes, auto-graded assignments, and verified tutoring in Egypt.",
                "telephone" => "+201000000000",
                "email" => "support@elite-academy.com",
                "address" => [
                    "@type" => "PostalAddress",
                    "streetAddress" => "Academic Center Tower, New Cairo",
                    "addressLocality" => "Cairo",
                    "addressCountry" => "EG"
                ],
                "contactPoint" => [
                    [
                        "@type" => "ContactPoint",
                        "telephone" => "+201000000000",
                        "contactType" => "customer service",
                        "availableLanguage" => ["Arabic", "English"],
                        "areaServed" => "EG"
                    ]
                ],
                "aggregateRating" => [
                    "@type" => "AggregateRating",
                    "ratingValue" => "4.9",
                    "reviewCount" => "1280",
                    "bestRating" => "5",
                    "worstRating" => "1"
                ],
                "sameAs" => [
                    "https://facebook.com/eliteacademy",
                    "https://twitter.com/eliteacademy",
                    "https://instagram.com/eliteacademy"
                ]
            ],
            [
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
            ],
            [
                "@type" => "FAQPage",
                "@id" => url('/') . "/#faq",
                "mainEntity" => [
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'ما هي أكاديمية إيليت التعليمية؟' : 'What is Elite Academy LMS?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'أكاديمية إيليت هي المنصة التعليمية الرقمية الرائدة في مصر لحصص البث المباشر المعتمدة، متابعة أولياء الأمور، والحل التفاعلي للواجبات بصفة لحظية.' 
                                : 'Elite Academy LMS is Egypt premier accredited K-12 interactive tutoring platform featuring live streaming, instant assignment solver, and real-time parent progress tracking.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'كيف تعمل حصص البث المباشر والتفاعلي؟' : 'How do live streaming interactive sessions work?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'يتصل الطلاب بالبث التفاعلي عبر زوم أو جيتسي مع علامة مائية أمنية ديناميكية لحماية المحتوى وتسجيل حضور تلقائي بالدقيقة.' 
                                : 'Students connect to encrypted live sessions powered by Zoom/Jitsi with dynamic security watermarking and minute-by-minute automatic attendance tracking.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'هل يتم تصحيح الواجبات والاختبارات تلقائياً؟' : 'Are assignments and quizzes auto-graded?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'نعم، تشمل المنصة واجهة تفاعلية لحل الواجبات مع حفظ المسودات تلقائياً وإمكانية التصحيح الفوري وإرسال التغذية الراجعة.' 
                                : 'Yes, assignments feature step-by-step solver interfaces with offline draft auto-saving, automated instant grading, and teacher reviews.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'كيف يمكن لأولياء الأمور متابعة مستوى الطالب؟' : 'How can parents track student academic progress?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'يمكن لولي الأمر ربط حساب الطالب برقم الهاتف عبر بوابة ولي الأمر لمتابعة نسبة الحضور، درجات الاختبارات، وتنبيهات الغياب اللحظية.' 
                                : 'Parents link their child account via phone number verification on the Parent Portal to monitor attendance, quiz scores, and real-time alerts.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'ما هي المراحل الدراسية والمواد المتاحة؟' : 'What grade levels and subjects are available?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'تغطي المنصة جميع مراحل الثانوية العامة واللغات في مواد الفيزياء، البرمجة، والذكاء الاصطناعي، الكيمياء، والرياضيات.' 
                                : 'We offer accredited tracks across Thanawya Amma secondary grades in Physics, Computer Science & AI, Chemistry, and Advanced Mathematics.'
                        ]
                    ]
                ]
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
