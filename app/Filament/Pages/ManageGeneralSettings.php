<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class ManageGeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات النظام العامة' : 'System Settings';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات المنظومة والهوية' : 'System & Brand Settings';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar'
            ? 'مركز إعدادات النظام والهوية البصرية الشاملة'
            : 'Central System Settings & Visual Identity Hub';
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return $user->isAdmin() || $user->hasRole('Super Admin') || $user->can('page_ManageGeneralSettings');
    }

    protected string $view = 'filament.pages.manage-general-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $quickLinksRaw = SiteSetting::get('footer_quick_links');
        $quickLinks = $quickLinksRaw ? json_decode($quickLinksRaw, true) : [
            ['label_ar' => 'الرئيسية', 'label_en' => 'Home', 'url' => '/'],
            ['label_ar' => 'التقييمات والآراء', 'label_en' => 'Reviews & Ratings', 'url' => '/reviews'],
            ['label_ar' => 'الأسئلة الشائعة', 'label_en' => 'FAQ & Help', 'url' => '/faq'],
            ['label_ar' => 'من نحن', 'label_en' => 'About Us', 'url' => '/about'],
            ['label_ar' => 'المعلمون', 'label_en' => 'Teachers', 'url' => '/teachers'],
            ['label_ar' => 'المدونة', 'label_en' => 'Blog', 'url' => '/blog'],
            ['label_ar' => 'خريطة الموقع', 'label_en' => 'Sitemap XML', 'url' => '/sitemap.xml'],
            ['label_ar' => 'بوابة الطلاب', 'label_en' => 'Student Portal', 'url' => '/student-portal'],
        ];

        $subjectsLinksRaw = SiteSetting::get('footer_subjects_links');
        $subjectsLinks = $subjectsLinksRaw ? json_decode($subjectsLinksRaw, true) : [
            ['label_ar' => 'البرمجة', 'label_en' => 'Programming', 'url' => '/subjects'],
            ['label_ar' => 'الذكاء الاصطناعي', 'label_en' => 'Artificial Intelligence', 'url' => '/subjects'],
            ['label_ar' => 'العلوم والفيزياء', 'label_en' => 'Science & Physics', 'url' => '/subjects'],
            ['label_ar' => 'إدارة الأعمال', 'label_en' => 'Business Administration', 'url' => '/subjects'],
            ['label_ar' => 'التصميم الإبداعي', 'label_en' => 'Creative Design', 'url' => '/subjects'],
            ['label_ar' => 'الرياضيات', 'label_en' => 'Mathematics', 'url' => '/subjects'],
        ];

        $this->form->fill([
            // ── Tab 1: Identity & Branding ──
            'app_name_ar' => SiteSetting::get('app_name_ar', config('demo.branding.app_name_ar', 'أكاديمية إيليت التعليمية')),
            'app_name_en' => SiteSetting::get('app_name_en', config('demo.branding.app_name', 'Elite Academy')),
            'tagline_ar' => SiteSetting::get('tagline_ar', config('demo.branding.tagline_ar', 'المنصة الأكاديمية الأولى في مصر لتأهيل قادة المستقبل')),
            'tagline_en' => SiteSetting::get('tagline_en', config('demo.branding.tagline', 'Leading academic LMS platform empowering students across Egypt')),
            'site_logo_light' => SiteSetting::get('site_logo_light', SiteSetting::get('site_logo')),
            'site_logo_dark' => SiteSetting::get('site_logo_dark'),
            'site_favicon' => SiteSetting::get('site_favicon'),
            'site_app_icon' => SiteSetting::get('site_app_icon'),

            // ── Tab 2: Theme & Colors ──
            'theme_primary_color' => SiteSetting::get('theme_primary_color', config('demo.branding.primary_color', '#0d9488')),
            'theme_secondary_color' => SiteSetting::get('theme_secondary_color', config('demo.branding.secondary_color', '#6366f1')),
            'theme_accent_color' => SiteSetting::get('theme_accent_color', config('demo.branding.accent_color', '#10b981')),
            'theme_surface_mode' => SiteSetting::get('theme_surface_mode', 'dark-glass'),
            'theme_btn_radius' => SiteSetting::get('theme_btn_radius', 'full'),
            'theme_card_radius' => SiteSetting::get('theme_card_radius', '2xl'),

            // ── Tab 3: Currency & Localization ──
            'currency_code' => SiteSetting::get('currency_code', 'EGP'),
            'currency_symbol_ar' => SiteSetting::get('currency_symbol_ar', 'ج.م'),
            'currency_symbol_en' => SiteSetting::get('currency_symbol_en', 'EGP'),
            'currency_position' => SiteSetting::get('currency_position', 'after'),
            'default_locale' => SiteSetting::get('default_locale', 'ar'),
            'timezone' => SiteSetting::get('timezone', 'Africa/Cairo'),

            // ── Tab 4: Contact & Organization ──
            'contact_email' => SiteSetting::get('contact_email', config('demo.branding.support_email', 'contact@elite-academy.edu.eg')),
            'contact_phone' => SiteSetting::get('contact_phone', config('demo.branding.support_phone', '+20 100 000 0000')),
            'contact_whatsapp' => SiteSetting::get('contact_whatsapp', 'https://wa.me/201000000000'),
            'contact_address_ar' => SiteSetting::get('contact_address_ar', 'برج الأكاديمية، القاهرة الجديدة، مصر'),
            'contact_address_en' => SiteSetting::get('contact_address_en', 'Academic Center Tower, New Cairo, Egypt'),
            'footer_working_hours_ar' => SiteSetting::get('footer_working_hours_ar', 'السبت - الخميس: 9:00 ص - 9:00 م'),
            'footer_working_hours_en' => SiteSetting::get('footer_working_hours_en', 'Sat - Thu: 9:00 AM - 9:00 PM'),
            'footer_rights_ar' => SiteSetting::get('footer_rights_ar', '© 2026 أكاديمية إيليت التعليمية. جميع الحقوق محفوظة.'),
            'footer_rights_en' => SiteSetting::get('footer_rights_en', '© 2026 Elite Academy. All rights reserved.'),

            // ── Tab 5: Social Media Channels ──
            'social_facebook' => SiteSetting::get('social_facebook', 'https://facebook.com/eliteacademy'),
            'social_twitter' => SiteSetting::get('social_twitter', 'https://twitter.com/eliteacademy'),
            'social_instagram' => SiteSetting::get('social_instagram', 'https://instagram.com/eliteacademy'),
            'social_linkedin' => SiteSetting::get('social_linkedin', 'https://linkedin.com/company/eliteacademy'),
            'social_youtube' => SiteSetting::get('social_youtube', 'https://youtube.com/@eliteacademy'),
            'social_whatsapp' => SiteSetting::get('social_whatsapp', 'https://wa.me/201000000000'),
            'social_tiktok' => SiteSetting::get('social_tiktok', 'https://tiktok.com/@eliteacademy'),
            'social_telegram' => SiteSetting::get('social_telegram', 'https://t.me/eliteacademy'),

            // ── Tab 6: Footer CMS ──
            'footer_tagline_ar' => SiteSetting::get('footer_tagline_ar', 'منصة إيليت التعليمية الرائدة في تقديم مسارات البرمجة، الذكاء الاصطناعي، والعلوم المعتمدة بأحدث وسائل التكنولوجيا التفاعلية.'),
            'footer_tagline_en' => SiteSetting::get('footer_tagline_en', 'Elite Academy empowers Egyptian students with accredited academic tracks in Programming, AI, and Science led by top educators.'),
            'footer_quick_links_title_ar' => SiteSetting::get('footer_quick_links_title_ar', 'روابط سريعة'),
            'footer_quick_links_title_en' => SiteSetting::get('footer_quick_links_title_en', 'Quick Links'),
            'footer_subjects_title_ar' => SiteSetting::get('footer_subjects_title_ar', 'المسارات الدراسية'),
            'footer_subjects_title_en' => SiteSetting::get('footer_subjects_title_en', 'Academic Tracks'),
            'footer_contact_title_ar' => SiteSetting::get('footer_contact_title_ar', 'معلومات التواصل'),
            'footer_contact_title_en' => SiteSetting::get('footer_contact_title_en', 'Contact Information'),
            'footer_quick_links' => $quickLinks,
            'footer_subjects_links' => $subjectsLinks,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';

        return $schema
            ->components([
                Tabs::make('SettingsTabs')
                    ->tabs([
                        // ── Tab 1: Identity & Branding ──
                        Tabs\Tab::make('IdentitySettings')
                            ->label($isAr ? 'الهوية والشعارات' : 'Identity & Logos')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make(new HtmlString('<i class="fa-solid fa-graduation-cap text-teal-400 me-2"></i>' . ($isAr ? 'الاسم الرسمي والشعار اللفظي' : 'Official Name & Brand Slogan')))
                                    ->description($isAr
                                        ? 'يتم تطبيق اسم وشعار المنصة تلقائياً عبر لوحة التحكم، صفحات تسجيل الدخول، رأس وتذييل الموقع، وبوابات الطلاب والمعلمين وأولياء الأمور.'
                                        : 'Official platform names and slogans sync instantly across Admin header, Auth login cards, Navbar, Footer, and Portals.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('app_name_ar')
                                                ->label($isAr ? 'اسم المنصة / الأكاديمية (بالعربية)' : 'Platform Name (Arabic)')
                                                ->placeholder('أكاديمية إيليت التعليمية')
                                                ->required(),

                                            TextInput::make('app_name_en')
                                                ->label($isAr ? 'اسم المنصة / الأكاديمية (بالإنجليزية)' : 'Platform Name (English)')
                                                ->placeholder('Elite Academy')
                                                ->required(),

                                            TextInput::make('tagline_ar')
                                                ->label($isAr ? 'الشعار اللفظي / الوصف المختصر (بالعربية)' : 'Brand Tagline (Arabic)')
                                                ->placeholder('المنصة الأكاديمية الأولى في مصر لتأهيل قادة المستقبل'),

                                            TextInput::make('tagline_en')
                                                ->label($isAr ? 'الشعار اللفظي / الوصف المختصر (بالإنجليزية)' : 'Brand Tagline (English)')
                                                ->placeholder('Leading academic LMS platform empowering students across Egypt'),
                                        ]),
                                    ]),

                                Section::make(new HtmlString('<i class="fa-solid fa-images text-teal-400 me-2"></i>' . ($isAr ? 'ملفات الشعارات والأيقونات الرسمية' : 'Official Logos & Favicon Files')))
                                    ->description($isAr
                                        ? 'قم برفع الشعارات المعتمدة بصيغ PNG أو SVG أو WEBP عالية الدقة وبخلفية شفافة. التغييرات تنعكس فورياً في كامل المنظومة.'
                                        : 'Upload approved brand logos in high-resolution PNG, SVG or WEBP with transparent background. Changes sync system-wide.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            FileUpload::make('site_logo_light')
                                                ->label($isAr ? 'شعار الوضع الفاتح (Light Surface Logo)' : 'Light Mode Logo')
                                                ->helperText($isAr ? 'يظهر على الخلفيات البيضاء والفاتحة في الهيدر والشهادات والتقارير.' : 'Used on white/light backgrounds in headers, certificates, and printable reports.')
                                                ->disk('public')
                                                ->directory('branding')
                                                ->visibility('public')
                                                ->image()
                                                ->maxSize(5120),

                                            FileUpload::make('site_logo_dark')
                                                ->label($isAr ? 'شعار الوضع الداكن والزجاجي (Dark / Glass Logo)' : 'Dark / Glass Mode Logo')
                                                ->helperText($isAr ? 'يظهر في لوحة التحكم بالوضع الليلي وفي الفوتر الداكن وأشرطة التنقل الزجاجية.' : 'Used in dark mode admin panel, dark footer, and glassmorphic shells.')
                                                ->disk('public')
                                                ->directory('branding')
                                                ->visibility('public')
                                                ->image()
                                                ->maxSize(5120),

                                            FileUpload::make('site_favicon')
                                                ->label($isAr ? 'أيقونة تبويب المتصفح (Favicon .ico / .png / .svg)' : 'Browser Favicon')
                                                ->helperText($isAr ? 'الأيقونة المصغرة التي تظهر في شريط عناوين وتبويبات المتصفح (16x16 / 32x32 / 64x64).' : 'Tiny icon in browser tab and bookmarks.')
                                                ->disk('public')
                                                ->directory('branding')
                                                ->visibility('public')
                                                ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/svg+xml', 'image/webp'])
                                                ->maxSize(2048),

                                            FileUpload::make('site_app_icon')
                                                ->label($isAr ? 'أيقونة الهواتف وتطبيقات الويب (PWA App Icon 512x512)' : 'Mobile PWA App Icon')
                                                ->helperText($isAr ? 'أيقونة مربعة عالية الدقة تظهر عند تثبيت الموقع كتطبيق على شاشات الهواتف الذكية.' : 'High-res square icon for mobile homescreen shortcuts and PWA.')
                                                ->disk('public')
                                                ->directory('branding')
                                                ->visibility('public')
                                                ->image()
                                                ->maxSize(3072),
                                        ]),
                                    ]),
                            ]),

                        // ── Tab 2: Theme Colors & Visual System ──
                        Tabs\Tab::make('ThemeSettings')
                            ->label($isAr ? 'المظهر وألوان النظام' : 'Theme & Colors')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make(new HtmlString('<i class="fa-solid fa-palette text-teal-400 me-2"></i>' . ($isAr ? 'لوحة الألوان الأساسية للمنظومة' : 'Core System Palette & Theme Tokens')))
                                    ->description($isAr
                                        ? 'تحكم في ألوان الهوية البصرية. هذه الألوان تغذي نظام Glassmorphism الذكي وأزرار الإجراءات والرسوم البيانية.'
                                        : 'Control brand identity colors. Dynamically drives the Smart Glassmorphism theme, action buttons, and telemetry.')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            ColorPicker::make('theme_primary_color')
                                                ->label($isAr ? 'اللون الأساسي (Primary Color)' : 'Primary Brand Color')
                                                ->default('#0d9488')
                                                ->required(),

                                            ColorPicker::make('theme_secondary_color')
                                                ->label($isAr ? 'اللون الثانوي (Secondary Accent)' : 'Secondary Accent Color')
                                                ->default('#6366f1')
                                                ->required(),

                                            ColorPicker::make('theme_accent_color')
                                                ->label($isAr ? 'لون الوهج والإبراز (Highlight Accent)' : 'Highlight & Glow Accent')
                                                ->default('#10b981')
                                                ->required(),
                                        ]),

                                        Grid::make(3)->schema([
                                            Select::make('theme_surface_mode')
                                                ->label($isAr ? 'النمط البصري العام للواجهات' : 'Global Surface Aesthetic')
                                                ->options([
                                                    'dark-glass'  => $isAr ? '💎 Smart Glassmorphism (زجاج بلوري ذكي)' : '💎 Smart Glassmorphism (Default)',
                                                    'deep-slate'  => $isAr ? '🌌 Deep Slate Modern (داكن عصري)' : '🌌 Deep Slate Modern',
                                                    'pure-dark'   => $isAr ? '🖤 Pure OLED Black (أسود نقي)' : '🖤 Pure OLED Black',
                                                    'clean-light' => $isAr ? '☀️ Clean Crisp Light (فاتح ناصع)' : '☀️ Clean Crisp Light',
                                                ])
                                                ->default('dark-glass')
                                                ->native(false),

                                            Select::make('theme_btn_radius')
                                                ->label($isAr ? 'انحناء حواف الأزرار (Button Radius)' : 'Button Corner Radius')
                                                ->options([
                                                    'full' => $isAr ? 'كبسولي كامل (Pill - 9999px)' : 'Full Pill (9999px)',
                                                    'lg'   => $isAr ? 'منحني عريض (Large - 12px)' : 'Large Rounded (12px)',
                                                    'md'   => $isAr ? 'متوسط متوازن (Medium - 8px)' : 'Medium Rounded (8px)',
                                                    'none' => $isAr ? 'مستطيل حاد (Sharp - 0px)' : 'Sharp Square (0px)',
                                                ])
                                                ->default('full')
                                                ->native(false),

                                            Select::make('theme_card_radius')
                                                ->label($isAr ? 'انحناء بطاقات النظام الزجاجية' : 'Glass Card Radius')
                                                ->options([
                                                    '2xl' => $isAr ? 'زجاجي انسيابي (Extra Curved - 20px)' : 'Glass Curve (20px)',
                                                    'xl'  => $isAr ? 'عصري قياسي (Modern - 14px)' : 'Modern Rounded (14px)',
                                                    'lg'  => $isAr ? 'ناعم قياسي (Standard - 10px)' : 'Standard (10px)',
                                                ])
                                                ->default('2xl')
                                                ->native(false),
                                        ]),
                                    ]),
                            ]),

                        // ── Tab 3: Currency & Regional Localization ──
                        Tabs\Tab::make('CurrencyLocalizationSettings')
                            ->label($isAr ? 'العملة والإعدادات الإقليمية' : 'Currency & Localization')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make(new HtmlString('<i class="fa-solid fa-coins text-teal-400 me-2"></i>' . ($isAr ? 'إعدادات العملة والفوترة والمنطقة الزمنية' : 'Currency, Billing & Timezone Settings')))
                                    ->description($isAr
                                        ? 'يتم تطبيق العملة ورموزها في كل أنحاء النظام: أسعار الكورسات، الباقات، التقارير المالية، والفواتير.'
                                        : 'Active currency settings applied across packages, pricing, financials, and invoice receipts.')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Select::make('currency_code')
                                                ->label($isAr ? 'كود العملة الدولي (ISO Code)' : 'Currency Code (ISO)')
                                                ->options([
                                                    'EGP' => 'EGP — الجنيه المصري (Egyptian Pound)',
                                                    'USD' => 'USD — الدولار الأمريكي (US Dollar)',
                                                    'SAR' => 'SAR — الريال السعودي (Saudi Riyal)',
                                                    'AED' => 'AED — الدرهم الإماراتي (UAE Dirham)',
                                                    'KWD' => 'KWD — الدينار الكويتي (Kuwaiti Dinar)',
                                                    'QAR' => 'QAR — الريال القطري (Qatari Riyal)',
                                                    'EUR' => 'EUR — اليورو الأوروبي (Euro)',
                                                    'GBP' => 'GBP — الجنيه الإسترليني (British Pound)',
                                                ])
                                                ->default('EGP')
                                                ->searchable()
                                                ->required(),

                                            TextInput::make('currency_symbol_ar')
                                                ->label($isAr ? 'رمز العملة بالعربية (Arabic Symbol)' : 'Currency Symbol (Arabic)')
                                                ->placeholder('ج.م')
                                                ->required(),

                                            TextInput::make('currency_symbol_en')
                                                ->label($isAr ? 'رمز العملة بالإنجليزية (English Symbol)' : 'Currency Symbol (English)')
                                                ->placeholder('EGP')
                                                ->required(),
                                        ]),

                                        Grid::make(3)->schema([
                                            Select::make('currency_position')
                                                ->label($isAr ? 'موضع رمز العملة' : 'Symbol Position')
                                                ->options([
                                                    'after'  => $isAr ? 'بعد المبلغ (مثال: 500 ج.م / 500 EGP)' : 'After Amount (e.g. 500 EGP)',
                                                    'before' => $isAr ? 'قبل المبلغ (مثال: ج.م 500 / EGP 500)' : 'Before Amount (e.g. EGP 500)',
                                                ])
                                                ->default('after')
                                                ->native(false),

                                            Select::make('default_locale')
                                                ->label($isAr ? 'اللغة الافتراضية للزوار والمنصة' : 'Default Platform Language')
                                                ->options([
                                                    'ar' => $isAr ? 'العربية (Arabic - RTL)' : 'Arabic (RTL)',
                                                    'en' => $isAr ? 'الإنجليزية (English - LTR)' : 'English (LTR)',
                                                ])
                                                ->default('ar')
                                                ->native(false),

                                            Select::make('timezone')
                                                ->label($isAr ? 'المنطقة الزمنية الرسمية (System Timezone)' : 'System Timezone')
                                                ->options([
                                                    'Africa/Cairo'  => 'القاهرة (Africa/Cairo — GMT+2)',
                                                    'Asia/Riyadh'   => 'الرياض / مكة المكرمة (Asia/Riyadh — GMT+3)',
                                                    'Asia/Dubai'    => 'دبي / أبوظبي (Asia/Dubai — GMT+4)',
                                                    'Asia/Kuwait'   => 'الكويت (Asia/Kuwait — GMT+3)',
                                                    'UTC'           => 'التوقيت العالمي المنسق (UTC)',
                                                    'Europe/London' => 'لندن (Europe/London — GMT+0/+1)',
                                                ])
                                                ->default('Africa/Cairo')
                                                ->searchable(),
                                        ]),
                                    ]),
                            ]),

                        // ── Tab 4: Contact & Organization Details ──
                        Tabs\Tab::make('ContactInfoSettings')
                            ->label($isAr ? 'بيانات المنشأة والتواصل' : 'Contact & Organization')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Section::make(new HtmlString('<i class="fa-solid fa-headset text-teal-400 me-2"></i>' . ($isAr ? 'بيانات الاتصال ومواعيد العمل وحقوق الملكية' : 'Contact, Hours & Copyright Details')))
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('contact_email')
                                                ->label($isAr ? 'البريد الإلكتروني الرسمي' : 'Official Contact Email')
                                                ->email()
                                                ->placeholder('contact@elite-academy.edu.eg'),

                                            TextInput::make('contact_phone')
                                                ->label($isAr ? 'رقم الهاتف / الخط الساخن' : 'Official Contact Phone')
                                                ->tel()
                                                ->placeholder('+20 100 000 0000'),

                                            TextInput::make('contact_whatsapp')
                                                ->label($isAr ? 'رقم / رابط الواتساب المباشر' : 'Official WhatsApp Link')
                                                ->placeholder('https://wa.me/201000000000'),
                                        ]),

                                        Grid::make(2)->schema([
                                            TextInput::make('contact_address_ar')
                                                ->label($isAr ? 'العنوان الجغرافي (بالعربية)' : 'Address (Arabic)'),

                                            TextInput::make('contact_address_en')
                                                ->label($isAr ? 'العنوان الجغرافي (بالإنجليزية)' : 'Address (English)'),

                                            TextInput::make('footer_working_hours_ar')
                                                ->label($isAr ? 'مواعيد العمل (بالعربية)' : 'Working Hours (Arabic)'),

                                            TextInput::make('footer_working_hours_en')
                                                ->label($isAr ? 'مواعيد العمل (بالإنجليزية)' : 'Working Hours (English)'),

                                            TextInput::make('footer_rights_ar')
                                                ->label($isAr ? 'حقوق النشر والملكية (بالعربية)' : 'Copyright Notice (Arabic)'),

                                            TextInput::make('footer_rights_en')
                                                ->label($isAr ? 'حقوق النشر والملكية (بالإنجليزية)' : 'Copyright Notice (English)'),
                                        ]),
                                    ]),
                            ]),

                        // ── Tab 5: Social Media Channels ──
                        Tabs\Tab::make('SocialMediaSettings')
                            ->label($isAr ? 'روابط التواصل الاجتماعي' : 'Social Media')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make(new HtmlString('<i class="fa-solid fa-share-nodes text-teal-400 me-2"></i>' . ($isAr ? 'روابط حسابات التواصل الرسمية للمنظومة' : 'Official Social Media Profiles')))
                                    ->description($isAr
                                        ? 'يتم ربط هذه الحسابات تلقائياً بالأيقونات التفاعلية في الفوتر ورأس الصفحة ومراسلات الدعم الفني.'
                                        : 'These links dynamically power social icons across footer, contact pages, and support communications.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('social_facebook')
                                                ->label(new HtmlString('<i class="fa-brands fa-facebook-f text-[#1877F2] me-1"></i> ' . ($isAr ? 'فيسبوك (Facebook URL)' : 'Facebook URL')))
                                                ->placeholder('https://facebook.com/your-page')
                                                ->url(),

                                            TextInput::make('social_twitter')
                                                ->label(new HtmlString('<i class="fa-brands fa-x-twitter text-slate-800 dark:text-white me-1"></i> ' . ($isAr ? 'تويتر / إكس (Twitter / X URL)' : 'Twitter / X URL')))
                                                ->placeholder('https://twitter.com/your-handle')
                                                ->url(),

                                            TextInput::make('social_instagram')
                                                ->label(new HtmlString('<i class="fa-brands fa-instagram text-[#E1306C] me-1"></i> ' . ($isAr ? 'إنستغرام (Instagram URL)' : 'Instagram URL')))
                                                ->placeholder('https://instagram.com/your-profile')
                                                ->url(),

                                            TextInput::make('social_linkedin')
                                                ->label(new HtmlString('<i class="fa-brands fa-linkedin-in text-[#0A66C2] me-1"></i> ' . ($isAr ? 'لينكد إن (LinkedIn URL)' : 'LinkedIn URL')))
                                                ->placeholder('https://linkedin.com/company/your-company')
                                                ->url(),

                                            TextInput::make('social_youtube')
                                                ->label(new HtmlString('<i class="fa-brands fa-youtube text-[#FF0000] me-1"></i> ' . ($isAr ? 'يوتيوب (YouTube URL)' : 'YouTube URL')))
                                                ->placeholder('https://youtube.com/@your-channel')
                                                ->url(),

                                            TextInput::make('social_whatsapp')
                                                ->label(new HtmlString('<i class="fa-brands fa-whatsapp text-[#25D366] me-1"></i> ' . ($isAr ? 'واتساب (WhatsApp Link / wa.me)' : 'WhatsApp URL')))
                                                ->placeholder('https://wa.me/201000000000')
                                                ->url(),

                                            TextInput::make('social_tiktok')
                                                ->label(new HtmlString('<i class="fa-brands fa-tiktok me-1"></i> ' . ($isAr ? 'تيك توك (TikTok URL)' : 'TikTok URL')))
                                                ->placeholder('https://tiktok.com/@your-account')
                                                ->url(),

                                            TextInput::make('social_telegram')
                                                ->label(new HtmlString('<i class="fa-brands fa-telegram text-[#229ED9] me-1"></i> ' . ($isAr ? 'تيليجرام (Telegram Link)' : 'Telegram URL')))
                                                ->placeholder('https://t.me/your-channel')
                                                ->url(),
                                        ]),
                                    ]),
                            ]),

                        // ── Tab 6: Footer CMS & Navigation Links ──
                        Tabs\Tab::make('FooterContentSettings')
                            ->label($isAr ? 'محتوى وعناوين الفوتر' : 'Footer CMS')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                Section::make(new HtmlString('<i class="fa-solid fa-pen-to-square text-teal-400 me-2"></i>' . ($isAr ? 'نصوص وعناوين أعمدة الفوتر' : 'Footer Column Titles & Description')))
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Textarea::make('footer_tagline_ar')
                                                ->label($isAr ? 'وصف المنصة في الفوتر (بالعربية)' : 'Footer Tagline (Arabic)')
                                                ->rows(2),

                                            Textarea::make('footer_tagline_en')
                                                ->label($isAr ? 'وصف المنصة في الفوتر (بالإنجليزية)' : 'Footer Tagline (English)')
                                                ->rows(2),

                                            TextInput::make('footer_quick_links_title_ar')
                                                ->label($isAr ? 'عنوان عمود الروابط السريعة (عربي)' : 'Quick Links Title (Arabic)'),

                                            TextInput::make('footer_quick_links_title_en')
                                                ->label($isAr ? 'عنوان عمود الروابط السريعة (إنجليزي)' : 'Quick Links Title (English)'),

                                            TextInput::make('footer_subjects_title_ar')
                                                ->label($isAr ? 'عنوان عمود المسارات والكورسات (عربي)' : 'Tracks Title (Arabic)'),

                                            TextInput::make('footer_subjects_title_en')
                                                ->label($isAr ? 'عنوان عمود المسارات والكورسات (إنجليزي)' : 'Tracks Title (English)'),

                                            TextInput::make('footer_contact_title_ar')
                                                ->label($isAr ? 'عنوان عمود التواصل (عربي)' : 'Contact Title (Arabic)'),

                                            TextInput::make('footer_contact_title_en')
                                                ->label($isAr ? 'عنوان عمود التواصل (إنجليزي)' : 'Contact Title (English)'),
                                        ]),
                                    ]),

                                Section::make(new HtmlString('<i class="fa-solid fa-list-check text-teal-400 me-2"></i>' . ($isAr ? 'قائمة الروابط السريعة بالفوتر (Quick Links)' : 'Footer Quick Links Repeater')))
                                    ->schema([
                                        Repeater::make('footer_quick_links')
                                            ->label($isAr ? 'الروابط السريعة' : 'Quick Navigation Links')
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    TextInput::make('label_ar')->label($isAr ? 'اسم الرابط (عربي)' : 'Label (Arabic)')->required(),
                                                    TextInput::make('label_en')->label($isAr ? 'اسم الرابط (إنجليزي)' : 'Label (English)')->required(),
                                                    TextInput::make('url')->label($isAr ? 'الرابط الموجه إليه' : 'Target URL')->required(),
                                                ]),
                                            ])
                                            ->reorderable(true)
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make(new HtmlString('<i class="fa-solid fa-book-bookmark text-teal-400 me-2"></i>' . ($isAr ? 'قائمة روابط المسارات والمقررات (Tracks Links)' : 'Footer Tracks & Subjects Links')))
                                    ->schema([
                                        Repeater::make('footer_subjects_links')
                                            ->label($isAr ? 'روابط المسارات والمقررات' : 'Tracks Links')
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    TextInput::make('label_ar')->label($isAr ? 'اسم المسار (عربي)' : 'Label (Arabic)')->required(),
                                                    TextInput::make('label_en')->label($isAr ? 'اسم المسار (إنجليزي)' : 'Label (English)')->required(),
                                                    TextInput::make('url')->label($isAr ? 'الرابط الموجه إليه' : 'Target URL')->required(),
                                                ]),
                                            ])
                                            ->reorderable(true)
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $logoKeys = ['site_logo_light', 'site_logo_dark', 'site_favicon', 'site_app_icon'];

        foreach ($state as $k => $v) {
            $group = in_array($k, $logoKeys)
                ? 'branding'
                : (str_starts_with($k, 'theme_')
                    ? 'theme'
                    : (str_starts_with($k, 'currency_')
                        ? 'billing'
                        : 'general'));

            if (is_array($v)) {
                // If it's a file upload field that returned an array with a single path
                if (in_array($k, $logoKeys) && count($v) > 0) {
                    $first = reset($v);
                    SiteSetting::set($k, is_string($first) ? $first : '', $group);
                } else {
                    SiteSetting::set($k, json_encode($v, JSON_UNESCAPED_UNICODE), $group);
                }
            } else {
                SiteSetting::set($k, is_bool($v) ? ($v ? '1' : '0') : (string) ($v ?? ''), $group);
            }
        }

        // Backward compatibility: keep site_logo in sync with site_logo_light
        if (isset($state['site_logo_light']) && ! empty($state['site_logo_light'])) {
            $lightVal = is_array($state['site_logo_light']) ? reset($state['site_logo_light']) : $state['site_logo_light'];
            SiteSetting::set('site_logo', (string) $lightVal, 'branding');
        }

        // Invalidate settings caches across runtime & memory
        Cache::forget('site_settings_dict');

        Notification::make()
            ->title(app()->getLocale() === 'ar' ? 'تم حفظ كافة إعدادات المنظومة بنجاح!' : 'System Settings Saved Successfully!')
            ->body(app()->getLocale() === 'ar'
                ? 'تم تحديث الهوية البصرية، الشعارات، الألوان، العملة، وعناصر المنظومة ومزامنتها بنجاح.'
                : 'Brand identity, logos, system colors, currency, and configurations synced system-wide.')
            ->success()
            ->send();
    }
}
