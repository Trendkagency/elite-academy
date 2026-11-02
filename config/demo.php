<?php

return [

    'enabled' => (bool) env('DEMO', false),

    /* ---------- Branding ---------- */
    'branding' => [
        'app_name' => env('DEMO_APP_NAME', 'EDU Demo'),
        'app_name_ar' => env('DEMO_APP_NAME_AR', env('DEMO_APP_NAME', 'EDU (نسخة تجريبية)')),
        'tagline' => 'Learn. Practice. Succeed.',
        'tagline_ar' => 'تعلّم. تدرّب. انجح.',
        'logo' => 'demo/logo.svg',
        'logo_dark' => 'demo/logo-dark.svg',
        'favicon' => 'demo/favicon.ico',
        'primary_color' => '#2563eb',
        'accent_color' => '#f59e0b',
        'footer_text' => env('DEMO_FOOTER_TEXT', '© ' . env('DEMO_APP_NAME', 'EDU Demo') . ' — for presentation only'),
        'support_email' => 'demo@example.com',
        'support_phone' => '+20 100 000 0000',
        'currency' => ['code' => 'EGP', 'symbol_en' => 'EGP', 'symbol_ar' => 'ج.م'],
    ],

    /* ---------- Section titles ---------- */
    'titles' => [
        'landing' => ['en' => 'Welcome to the Demo', 'ar' => 'مرحبًا بك في النسخة التجريبية'],
        'login' => ['en' => 'Demo Login', 'ar' => 'تسجيل دخول تجريبي'],
        'student_portal' => ['en' => 'Student Demo Portal', 'ar' => 'بوابة الطالب التجريبية'],
        'teacher_portal' => ['en' => 'Teacher Demo Portal', 'ar' => 'بوابة المعلم التجريبية'],
        'parent_portal' => ['en' => 'Parent Demo Portal', 'ar' => 'بوابة ولي الأمر التجريبية'],
        'admin_panel' => ['en' => 'Demo Admin', 'ar' => 'لوحة الإدارة التجريبية'],
    ],

    /* ---------- LOGIN PAGE DESIGN (public login + Filament admin login) ---------- */
    'login' => [
        'layout' => 'split',            // split | centered | card-left | card-right
        'heading' => ['en' => 'Try EDU', 'ar' => 'جرّب أكاديمية إيليت'],
        'subheading' => ['en' => 'Pick a role and sign in instantly.', 'ar' => 'اختر دورًا وسجّل الدخول فورًا.'],
        'show_logo' => true,
        'logo_height' => '4rem',
        'background' => [
            'type' => 'gradient', // image | gradient | color
            'image' => 'demo/login-bg.jpg',
            'gradient' => ['#0f172a', '#2563eb'],
            'color' => '#0f172a',
            'overlay_opacity' => 0.45,
        ],
        'card' => [
            'width' => '28rem',
            'radius' => '1rem',
            'blur' => true,
            'shadow' => 'xl',                // none | md | xl
        ],
        'side_panel' => [                    // used by layout=split
            'enabled' => true,
            'title' => ['en' => 'Everything you need to teach online', 'ar' => 'كل ما تحتاجه للتعليم أونلاين'],
            'bullets' => [
                ['en' => 'Live sessions', 'ar' => 'حصص مباشرة'],
                ['en' => 'Assignments & grading', 'ar' => 'واجبات وتصحيح'],
                ['en' => 'Parent follow-up', 'ar' => 'متابعة ولي الأمر'],
            ],
        ],
        'show_manual_form' => true,          // keep email/password form visible
    ],

    /* ---------- DIRECT (ONE-CLICK) LOGIN + CREDENTIALS ---------- */
    'credentials' => [
        'show_on_login' => true,
        'show_table' => true,             // show email + password list
        'password' => 'Demo@12345',
        'direct_login' => [
            'enabled' => true,              // clicking a role signs in with no typing
            'style' => 'cards',           // cards | buttons | list
            'throttle' => '10,1',
        ],
        'accounts' => [
            ['role' => 'admin', 'label' => ['en' => 'Admin', 'ar' => 'مدير'], 'email' => 'admin@demo.test', 'redirect' => '/admin'],
            ['role' => 'teacher', 'label' => ['en' => 'Teacher', 'ar' => 'معلم'], 'email' => 'teacher@demo.test', 'redirect' => '/teacher'],
            ['role' => 'student', 'label' => ['en' => 'Student', 'ar' => 'طالب'], 'email' => 'student@demo.test', 'redirect' => '/student'],
            ['role' => 'parent', 'label' => ['en' => 'Parent', 'ar' => 'ولي أمر'], 'email' => 'parent@demo.test', 'redirect' => '/parent'],
        ],
    ],

    /* ---------- Demo data volume ---------- */
    'seed' => [
        'teachers' => 6,
        'students' => 30,
        'parents' => 20,
        'courses' => 8,
        'sessions_per_course' => 12,
        'assignments_per_course' => 3,
        'packages' => 10,
        'articles' => 6,
        'hero_slides' => 3,
    ],

    /* ---------- Safety guards ---------- */
    'guards' => [
        'block_external_calls' => true,
        'block_destructive' => true,
        'show_banner' => true,
        'banner_text' => 'Demo mode — data resets automatically.',
    ],

    /* ---------- Auto reset ---------- */
    'reset' => ['enabled' => (bool) env('DEMO_AUTO_RESET', true)],
];
