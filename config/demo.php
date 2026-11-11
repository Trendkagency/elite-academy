<?php

return [

    'enabled' => (bool) env('DEMO', true),

    /* ---------- Branding ---------- */
    'branding' => [
        'app_name' => env('DEMO_APP_NAME', 'Elite Academy'),
        'app_name_ar' => env('DEMO_APP_NAME_AR', 'أكاديمية النخبة التعليمية'),
        'tagline' => 'Leading K-12 Interactive Education & Excellence Platform',
        'tagline_ar' => 'نحو تفوق دراسي حقيقي ومستقبل تعليمي رائد',
        'logo' => 'images/logo.png',
        'logo_dark' => 'images/logo.png',
        'favicon' => 'favicon.ico',
        'primary_color' => '#0d9488',
        'accent_color' => '#f59e0b',
        'footer_text' => env('DEMO_FOOTER_TEXT', '© 2026 أكاديمية النخبة التعليمية - جميع الحقوق محفوظة'),
        'support_email' => 'support@trendk.com',
        'support_phone' => '+20 100 000 0000',
        'currency' => ['code' => 'EGP', 'symbol_en' => 'EGP', 'symbol_ar' => 'ج.م'],
    ],

    /* ---------- Section titles ---------- */
    'titles' => [
        'landing' => ['en' => 'Elite Academy | Academic Excellence & Interactive Learning', 'ar' => 'أكاديمية النخبة التعليمية | منصة التفوق الدراسي والحصص التفاعلية'],
        'login' => ['en' => 'Elite Academy Portal Sign In', 'ar' => 'تسجيل الدخول - أكاديمية النخبة'],
        'student_portal' => ['en' => 'Student Learning Portal', 'ar' => 'بوابة الطالب الأكاديمية'],
        'teacher_portal' => ['en' => 'Teacher Management Portal', 'ar' => 'بوابة المعلم وإدارة الفصول'],
        'parent_portal' => ['en' => 'Parent Follow-up Portal', 'ar' => 'بوابة ولي الأمر ومتابعة التحصيل'],
        'admin_panel' => ['en' => 'Academy Administration Suite', 'ar' => 'لوحة الإدارة والحوكمة الأكاديمية'],
    ],

    /* ---------- LOGIN PAGE DESIGN (public login + Filament admin login) ---------- */
    'login' => [
        'layout' => 'split',            // split | centered | card-left | card-right
        'heading' => [
            'en' => 'Elite Academy Portal',
            'ar' => 'بوابة أكاديمية النخبة',
        ],
        'subheading' => [
            'en' => 'Select a demo role to sign in instantly or enter your credentials.',
            'ar' => 'اختر الحساب المطلوب للتجربة الفورية أو سجّل دخولك يدوياً.',
        ],
        'show_logo' => true,
        'logo_height' => '4rem',
        'background' => [
            'type' => 'gradient', // image | gradient | color
            'image' => 'demo/login-bg.jpg',
            'gradient' => ['#0f172a', '#0d9488'],
            'color' => '#0f172a',
            'overlay_opacity' => 0.45,
        ],
        'card' => [
            'width' => '28rem',
            'radius' => '1.25rem',
            'blur' => true,
            'shadow' => 'xl',                // none | md | xl
        ],
        'side_panel' => [                    // used by layout=split
            'enabled' => true,
            'title' => [
                'en' => 'Comprehensive EdTech Ecosystem for High Achievers',
                'ar' => 'المنظومة التعليمية الرقمية الأقوى لأوائل الجمهورية',
            ],
            'bullets' => [
                ['en' => 'Live Interactive HD Virtual Classrooms', 'ar' => 'حصص افتراضية تفاعلية مباشرة بجودة عالية'],
                ['en' => 'Instant Step-by-Step Assignment Auto-Grading', 'ar' => 'تصحيح تلقائي فوري لاختبارات وواجبات الثانوية'],
                ['en' => 'Parent 360° Real-time Academic Tracker', 'ar' => 'متابعة شاملة ولحظية لولي الأمر لنسب الحضور والدرجات'],
                ['en' => 'Financial Credit Balances & Transparent Ledgers', 'ar' => 'إدارة مالية دقيقة ورصيد حصص ومعاملات شفافة'],
            ],
        ],
        'show_manual_form' => true,          // keep email/password form visible
    ],

    /* ---------- DIRECT (ONE-CLICK) LOGIN + CREDENTIALS ---------- */
    'credentials' => [
        'show_on_login' => true,
        'show_table' => true,             // show email + password list
        'password' => 'password',
        'direct_login' => [
            'enabled' => true,              // clicking a role signs in with no typing
            'style' => 'cards',           // cards | buttons | list
            'throttle' => '10,1',
        ],
        'accounts' => [
            ['role' => 'admin', 'label' => ['en' => 'Admin (Principal)', 'ar' => 'المدير العام'], 'email' => 'admin@trendk.com', 'redirect' => '/admin'],
            ['role' => 'teacher', 'label' => ['en' => 'Senior Teacher', 'ar' => 'كبير المعلمين'], 'email' => 'teacher@trendk.com', 'redirect' => '/teacher'],
            ['role' => 'student', 'label' => ['en' => 'Top Student', 'ar' => 'الطالب المتفوق'], 'email' => 'student@trendk.com', 'redirect' => '/student'],
            ['role' => 'parent', 'label' => ['en' => 'Active Parent', 'ar' => 'ولي الأمر'], 'email' => 'parent@trendk.com', 'redirect' => '/parent'],
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
        'banner_text' => 'النسخة التجريبية المعتمدة — أكاديمية النخبة التعليمية (Trendk Demo).',
    ],

    /* ---------- Auto reset ---------- */
    'reset' => ['enabled' => (bool) env('DEMO_AUTO_RESET', true)],
];
