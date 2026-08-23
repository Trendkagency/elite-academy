<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageLandingPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static ?int $navigationSort = 0;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة المحتوى والموقع' : 'Landing Page CMS';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الصفحة الرئيسية والهوية' : 'Landing Page CMS';
    }

    protected string $view = 'filament.pages.manage-landing-page';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'landing_hero_badge_en',
            'landing_hero_badge_ar',
            'landing_hero_title_en',
            'landing_hero_title_ar',
            'landing_hero_subtitle_en',
            'landing_hero_subtitle_ar',
            'landing_cta_primary_text_en',
            'landing_cta_primary_text_ar',
            'landing_cta_primary_link',
            'announcement_enabled',
            'announcement_text_en',
            'announcement_text_ar',
            'announcement_link',
            'why_badge_en',
            'why_badge_ar',
            'why_title_en',
            'why_title_ar',
            'why_subtitle_en',
            'why_subtitle_ar',
            'about_badge_en',
            'about_badge_ar',
            'about_title_en',
            'about_title_ar',
            'about_content_en',
            'about_content_ar',
            'about_image',
            'subjects_title_en',
            'subjects_title_ar',
            'subjects_subtitle_en',
            'subjects_subtitle_ar',
            'teachers_title_en',
            'teachers_title_ar',
            'teachers_subtitle_en',
            'teachers_subtitle_ar',
            'testimonials_title_en',
            'testimonials_title_ar',
            'testimonials_subtitle_en',
            'testimonials_subtitle_ar',
            'cta_badge_en',
            'cta_badge_ar',
            'cta_headline_en',
            'cta_headline_ar',
            'cta_subtitle_en',
            'cta_subtitle_ar',
            'cta_bg_image',
            'footer_tagline_ar',
            'footer_tagline_en',
            'footer_quick_links_title_ar',
            'footer_quick_links_title_en',
            'footer_subjects_title_ar',
            'footer_subjects_title_en',
            'footer_contact_title_ar',
            'footer_contact_title_en',
            'footer_working_hours_ar',
            'footer_working_hours_en',
            'footer_rights_ar',
            'footer_rights_en',
            'contact_phone',
            'contact_email',
            'contact_address_en',
            'contact_address_ar',
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_linkedin',
            'social_youtube',
            'theme_primary_color',
        ];

        $formData = [];
        foreach ($keys as $k) {
            $formData[$k] = SiteSetting::get($k, $this->getDefaultValue($k));
        }

        $rawLayout = SiteSetting::get('sections_layout');
        $formData['sections_layout'] = $rawLayout ? json_decode($rawLayout, true) : $this->getDefaultValue('sections_layout');

        $rawStats = SiteSetting::get('landing_stats_counters');
        $formData['landing_stats_counters'] = $rawStats ? json_decode($rawStats, true) : $this->getDefaultValue('landing_stats_counters');

        $rawWhyItems = SiteSetting::get('landing_why_items');
        $formData['landing_why_items'] = $rawWhyItems ? json_decode($rawWhyItems, true) : $this->getDefaultValue('landing_why_items');

        $rawQuick = SiteSetting::get('footer_quick_links');
        $formData['footer_quick_links'] = $rawQuick ? json_decode($rawQuick, true) : $this->getDefaultValue('footer_quick_links');

        $rawSubjects = SiteSetting::get('footer_subjects_links');
        $formData['footer_subjects_links'] = $rawSubjects ? json_decode($rawSubjects, true) : $this->getDefaultValue('footer_subjects_links');

        $this->form->fill($formData);
    }

    private function getDefaultValue(string $key): mixed
    {
        return match ($key) {
            'sections_layout' => [
                ['key' => 'hero-slider', 'is_enabled' => true],
                ['key' => 'stats-overlay', 'is_enabled' => true],
                ['key' => 'why-choose', 'is_enabled' => true],
                ['key' => 'about-preview', 'is_enabled' => true],
                ['key' => 'subjects-grid', 'is_enabled' => true],
                ['key' => 'teachers-marquee', 'is_enabled' => true],
                ['key' => 'testimonials', 'is_enabled' => true],
                ['key' => 'cta_section', 'is_enabled' => true],
            ],
            'landing_stats_counters' => [
                ['count' => '25,000+', 'label_ar' => 'الطلاب النشطين', 'label_en' => 'Active Students', 'color' => 'teal'],
                ['count' => '120+', 'label_ar' => 'الكورسات والمقررات المعتمدة', 'label_en' => 'Expert Courses', 'color' => 'teal'],
                ['count' => '45+', 'label_ar' => 'المعلمين والمحاضرين', 'label_en' => 'Instructors & Mentors', 'color' => 'teal'],
                ['count' => '98.5%', 'label_ar' => 'رضا أولياء الأمور', 'label_en' => 'Parent Satisfaction', 'color' => 'orange'],
                ['count' => '100%', 'label_ar' => 'شهادات دولية معتمدة', 'label_en' => 'Global Certifications', 'color' => 'teal'],
            ],
            'landing_why_items' => [
                ['title_ar' => 'حصص بث مباشر تفاعلية', 'title_en' => 'Interactive Live Sessions', 'desc_ar' => 'محاضرات أسبوعية وتفاعل مباشر مع المعلمين مع تسجيل تلقائي.', 'desc_en' => 'Weekly live lectures with instructor interaction and instant recordings.'],
                ['title_ar' => 'مناهج معتمدة ومطورة', 'title_en' => 'Accredited Curricula', 'desc_ar' => 'محتوى أكاديمي متخصص يربط الدراسات النظرية بالتطبيق العملي.', 'desc_en' => 'Specialized academic content connecting theoretical study with practical skills.'],
                ['title_ar' => 'متابعة وتقارير أولياء الأمور', 'title_en' => 'Parent Progress Tracking', 'desc_ar' => 'تقارير دورية لمتابعة مستوى الطالب الأكاديمي ونسب الحضور.', 'desc_en' => 'Regular progress reports tracking academic performance and attendance.'],
            ],
            'footer_quick_links' => [
                ['label_ar' => 'الرئيسية', 'label_en' => 'Home', 'url' => '/'],
                ['label_ar' => 'من نحن', 'label_en' => 'About Us', 'url' => '/about'],
                ['label_ar' => 'المعلمون', 'label_en' => 'Teachers', 'url' => '/teachers'],
                ['label_ar' => 'الفعاليات', 'label_en' => 'Events', 'url' => '/events'],
                ['label_ar' => 'المدونة', 'label_en' => 'Blog', 'url' => '/blog'],
                ['label_ar' => 'بوابة الطلاب', 'label_en' => 'Student Portal', 'url' => '/student-portal'],
            ],
            'footer_subjects_links' => [
                ['label_ar' => 'البرمجة', 'label_en' => 'Programming', 'url' => '/subjects'],
                ['label_ar' => 'الذكاء الاصطناعي', 'label_en' => 'Artificial Intelligence', 'url' => '/subjects'],
                ['label_ar' => 'العلوم والفيزياء', 'label_en' => 'Science & Physics', 'url' => '/subjects'],
                ['label_ar' => 'إدارة الأعمال', 'label_en' => 'Business Administration', 'url' => '/subjects'],
                ['label_ar' => 'التصميم الإبداعي', 'label_en' => 'Creative Design', 'url' => '/subjects'],
                ['label_ar' => 'الرياضيات', 'label_en' => 'Mathematics', 'url' => '/subjects'],
            ],
            'landing_hero_badge_en' => '🚀 EGYPT’S #1 ACADEMIC PLATFORM',
            'landing_hero_badge_ar' => '🚀 المنصة الأكاديمية الأولى في مصر',
            'landing_hero_title_en' => 'Empowering Future Leaders with Practical Academic Excellence',
            'landing_hero_title_ar' => 'نُمكّن قادة المستقبل بالتميز الأكاديمي والتطبيقي',
            'landing_hero_subtitle_en' => 'Join thousands of students learning Programming, Artificial Intelligence, Science, and Business from Egypt’s top educators.',
            'landing_hero_subtitle_ar' => 'انضم إلى آلاف الطلاب الذين يتعلمون البرمجة، والذكاء الاصطناعي، والعلوم، وإدارة الأعمال من أفضل معلمي مصر.',
            'landing_cta_primary_text_en' => 'Explore All Subjects →',
            'landing_cta_primary_text_ar' => 'استكشف كافة المواد الدراسية ←',
            'landing_cta_primary_link' => '/subjects',
            'announcement_enabled' => '1',
            'announcement_text_en' => '🎉 Fall Cohort 2026 Registration is Now Open! Enroll in Live Stream Sessions.',
            'announcement_text_ar' => '🎉 التقديم لدفعة خريف 2026 مفتوح الآن! اشترك في البث المباشر.',
            'announcement_link' => '/courses',
            'why_badge_en' => 'THE ELITE ADVANTAGE',
            'why_badge_ar' => 'مزايا أكاديمية إيليت',
            'why_title_en' => 'Why Students & Parents Choose Elite Academy',
            'why_title_ar' => 'لماذا يختار الطلاب وأولياء الأمور أكاديمية إيليت؟',
            'why_subtitle_en' => 'We combine rigorous academic standards with modern practical mentorship.',
            'why_subtitle_ar' => 'نجمع بين التفوق الأكاديمي والتطبيق العملي الحديث لإعداد المبتكرين.',
            'about_badge_en' => 'REDEFINING EDUCATION',
            'about_badge_ar' => 'إعادة تعريف التعليم الأكاديمي',
            'about_title_en' => 'Where Passion Meets Academic Mastery',
            'about_title_ar' => 'حيث يلتقي الشغف بالإتقان الأكاديمي',
            'about_content_en' => 'Elite Academy bridges secondary education and real-world innovation through interactive live streams, structured MCQs, and expert teacher mentorship.',
            'about_content_ar' => 'تجمع أكاديمية إيليت بين الدراسة الأكاديمية والتطبيق العملي من خلال حصص البث المباشر، والواجبات التفاعلية، وإرشاد نخبة المعلمين.',
            'subjects_title_en' => 'Explore Specialized Subjects & Programs',
            'subjects_title_ar' => 'استكشف المواد الدراسية المتخصصة',
            'subjects_subtitle_en' => 'Comprehensive academic curricula designed for excellence and practical mastery.',
            'subjects_subtitle_ar' => 'مناهج أكاديمية متكاملة مصممة خصيصاً للتفوق والتمكن العملي.',
            'teachers_title_en' => 'Meet Our Elite Mentors & Instructors',
            'teachers_title_ar' => 'تعرف على نخبة المعلمين والمرشدين الأكاديميين',
            'teachers_subtitle_en' => 'Learn directly from Egypt’s top educators and PhD instructors.',
            'teachers_subtitle_ar' => 'تعلم مباشرة من أفضل المعلمين والمحاضرين الأكاديميين في مصر.',
            'testimonials_title_en' => 'What Our Students & Parents Say',
            'testimonials_title_ar' => 'ماذا يقول طلابنا وأولياء أمورنا؟',
            'testimonials_subtitle_en' => 'Real reviews and inspiring success stories from the Elite Academy community.',
            'testimonials_subtitle_ar' => 'آراء حقيقية وتجارب نجاح ملهمة من مجتمع إيليت أكاديمي.',
            'cta_badge_en' => '🚀 READY TO START LEARNING?',
            'cta_badge_ar' => '🚀 هل أنت مستعد لبدء التعلم؟',
            'cta_headline_en' => 'Ready to Excel in Your Academic Journey?',
            'cta_headline_ar' => 'هل أنت مستعد للتفوق في رحلتك الأكاديمية؟',
            'cta_subtitle_en' => 'Join Elite Academy today and gain unlimited access to top teachers, interactive live streams, and accredited courses.',
            'cta_subtitle_ar' => 'انضم إلى أكاديمية إيليت اليوم واحصل على وصول غير محدود لأفضل المعلمين، والبث المباشر، والمقررات المعتمدة.',
            'footer_tagline_ar' => 'المنصة الأكاديمية التعليمية الأولى في مصر لإعداد وتأهيل قادة المستقبل.',
            'footer_tagline_en' => 'Egypt\'s leading educational platform empowering future innovators through practical learning.',
            'footer_quick_links_title_ar' => 'روابط سريعة',
            'footer_quick_links_title_en' => 'Quick Links',
            'footer_subjects_title_ar' => 'المواد الدراسية',
            'footer_subjects_title_en' => 'Subjects',
            'footer_contact_title_ar' => 'تواصل معنا',
            'footer_contact_title_en' => 'Contact Us',
            'footer_working_hours_ar' => 'الأحد - الخميس: 9:00 - 18:00',
            'footer_working_hours_en' => 'Sun - Thu: 9:00 - 18:00',
            'footer_rights_ar' => '© 2026 أكاديمية إيليت. جميع الحقوق محفوظة.',
            'footer_rights_en' => '© 2026 Elite Academy. All rights reserved.',
            'contact_phone' => '+20 100 000 0000',
            'contact_email' => 'info@eliteacademy.edu.eg',
            'contact_address_en' => 'New Cairo, Egypt',
            'contact_address_ar' => 'القاهرة الجديدة، مصر',
            'social_facebook' => 'https://facebook.com',
            'social_twitter' => 'https://twitter.com',
            'social_instagram' => 'https://instagram.com',
            'social_linkedin' => 'https://linkedin.com',
            'social_youtube' => 'https://youtube.com',
            'theme_primary_color' => '#0d9488',
            default => '',
        };
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('SettingsTabs')
                    ->tabs([
                        Tabs\Tab::make('Hero Banner (EN & AR)')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make('Main Hero Banner Content (English & Arabic)')
                                    ->description('Configure multi-lingual titles, subtitles, badges, and action buttons for the main landing hero banner.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('landing_hero_badge_ar')
                                                ->label('Hero Badge (Arabic / بالعربية)')
                                                ->placeholder('🚀 المنصة الأكاديمية الأولى في مصر'),
                                            TextInput::make('landing_hero_badge_en')
                                                ->label('Hero Badge (English)')
                                                ->placeholder('🚀 EGYPT’S #1 ACADEMIC PLATFORM'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('landing_hero_title_ar')
                                                ->label('Main Title / Headline (Arabic / بالعربية)')
                                                ->required(),
                                            TextInput::make('landing_hero_title_en')
                                                ->label('Main Title / Headline (English)')
                                                ->required(),
                                        ]),
                                        Grid::make(2)->schema([
                                            Textarea::make('landing_hero_subtitle_ar')
                                                ->label('Subtitle / Description (Arabic / بالعربية)')
                                                ->rows(3),
                                            Textarea::make('landing_hero_subtitle_en')
                                                ->label('Subtitle / Description (English)')
                                                ->rows(3),
                                        ]),
                                        Grid::make(3)->schema([
                                            TextInput::make('landing_cta_primary_text_ar')
                                                ->label('Primary Button Text (Arabic / بالعربية)'),
                                            TextInput::make('landing_cta_primary_text_en')
                                                ->label('Primary Button Text (English)'),
                                            TextInput::make('landing_cta_primary_link')
                                                ->label('Primary Button Link / URL'),
                                        ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Statistics & Metrics')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('📊 Statistics Counters & Dynamic Metrics')
                                    ->description('Manage numbers and multi-lingual labels for active students, accredited courses, expert teachers, parent satisfaction, and global certifications.')
                                    ->schema([
                                        Repeater::make('landing_stats_counters')
                                            ->label('Platform Counters & Highlights')
                                            ->schema([
                                                Grid::make(4)->schema([
                                                    TextInput::make('count')
                                                        ->label('Metric Value')
                                                        ->placeholder('e.g. 25,000+ or 98.5%')
                                                        ->required(),
                                                    TextInput::make('label_ar')
                                                        ->label('Label (Arabic)')
                                                        ->placeholder('الطلاب النشطين')
                                                        ->required(),
                                                    TextInput::make('label_en')
                                                        ->label('Label (English)')
                                                        ->placeholder('Active Students')
                                                        ->required(),
                                                    Select::make('color')
                                                        ->label('Accent Color')
                                                        ->options([
                                                            'teal' => 'Teal Brand Glow (#0d9488)',
                                                            'orange' => 'Orange Glow (#f97316)',
                                                            'emerald' => 'Emerald Green (#10b981)',
                                                        ])
                                                        ->default('teal'),
                                                ]),
                                            ])
                                            ->reorderable(true)
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Why Choose Section')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                Section::make('Why Students & Parents Choose Elite Academy')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('why_badge_ar')->label('Badge Text (Arabic)'),
                                            TextInput::make('why_badge_en')->label('Badge Text (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('why_title_ar')->label('Section Title (Arabic)'),
                                            TextInput::make('why_title_en')->label('Section Title (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Textarea::make('why_subtitle_ar')->label('Section Subtitle (Arabic)')->rows(2),
                                            Textarea::make('why_subtitle_en')->label('Section Subtitle (English)')->rows(2),
                                        ]),
                                        Repeater::make('landing_why_items')
                                            ->label('Why Choose Feature Bullet Points')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    TextInput::make('title_ar')->label('Feature Title (Arabic)')->required(),
                                                    TextInput::make('title_en')->label('Feature Title (English)')->required(),
                                                ]),
                                                Grid::make(2)->schema([
                                                    Textarea::make('desc_ar')->label('Description (Arabic)')->rows(2),
                                                    Textarea::make('desc_en')->label('Description (English)')->rows(2),
                                                ]),
                                            ])
                                            ->reorderable(true)
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('About Section')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('About Academy Overview (EN & AR)')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('about_badge_ar')->label('Badge Text (Arabic)'),
                                            TextInput::make('about_badge_en')->label('Badge Text (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('about_title_ar')->label('Section Title (Arabic)'),
                                            TextInput::make('about_title_en')->label('Section Title (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Textarea::make('about_content_ar')->label('Overview Content (Arabic)')->rows(4),
                                            Textarea::make('about_content_en')->label('Overview Content (English)')->rows(4),
                                        ]),
                                        FileUpload::make('about_image')
                                            ->label('About Section Photo (Drag & Drop)')
                                            ->disk('public')
                                            ->directory('landing-page')
                                            ->image(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Showcase Titles')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Section::make('Subjects Showcase Section Headings')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('subjects_title_ar')->label('Subjects Section Title (Arabic)'),
                                            TextInput::make('subjects_title_en')->label('Subjects Section Title (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Textarea::make('subjects_subtitle_ar')->label('Subjects Subtitle (Arabic)')->rows(2),
                                            Textarea::make('subjects_subtitle_en')->label('Subjects Subtitle (English)')->rows(2),
                                        ]),
                                    ]),

                                Section::make('Faculty & Instructors Marquee Headings')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('teachers_title_ar')->label('Teachers Section Title (Arabic)'),
                                            TextInput::make('teachers_title_en')->label('Teachers Section Title (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Textarea::make('teachers_subtitle_ar')->label('Teachers Subtitle (Arabic)')->rows(2),
                                            Textarea::make('teachers_subtitle_en')->label('Teachers Subtitle (English)')->rows(2),
                                        ]),
                                    ]),

                                Section::make('Testimonials & Reviews Headings')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('testimonials_title_ar')->label('Testimonials Title (Arabic)'),
                                            TextInput::make('testimonials_title_en')->label('Testimonials Title (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Textarea::make('testimonials_subtitle_ar')->label('Testimonials Subtitle (Arabic)')->rows(2),
                                            Textarea::make('testimonials_subtitle_en')->label('Testimonials Subtitle (English)')->rows(2),
                                        ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('CTA & Announcement')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Section::make('Call to Action Banner (EN & AR)')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('cta_badge_ar')->label('CTA Badge (Arabic)'),
                                            TextInput::make('cta_badge_en')->label('CTA Badge (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('cta_headline_ar')->label('CTA Headline (Arabic)'),
                                            TextInput::make('cta_headline_en')->label('CTA Headline (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Textarea::make('cta_subtitle_ar')->label('CTA Subtitle (Arabic)')->rows(3),
                                            Textarea::make('cta_subtitle_en')->label('CTA Subtitle (English)')->rows(3),
                                        ]),
                                        FileUpload::make('cta_bg_image')
                                            ->label('CTA Background Image')
                                            ->disk('public')
                                            ->directory('landing-page')
                                            ->image(),
                                    ]),

                                Section::make('Top Announcement Bar (EN & AR)')
                                    ->schema([
                                        Toggle::make('announcement_enabled')
                                            ->label('Enable Top Announcement Bar'),
                                        Grid::make(2)->schema([
                                            TextInput::make('announcement_text_ar')
                                                ->label('Banner Text Message (Arabic / بالعربية)'),
                                            TextInput::make('announcement_text_en')
                                                ->label('Banner Text Message (English)'),
                                        ]),
                                        TextInput::make('announcement_link')
                                            ->label('Banner Link / Action URL'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Dynamic Footer CMS')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                Section::make('Footer Branding & Column Titles')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Textarea::make('footer_tagline_ar')->label('Footer Description / Tagline (Arabic)')->rows(2),
                                            Textarea::make('footer_tagline_en')->label('Footer Description / Tagline (English)')->rows(2),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('footer_quick_links_title_ar')->label('Quick Links Column Title (Arabic)'),
                                            TextInput::make('footer_quick_links_title_en')->label('Quick Links Column Title (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('footer_subjects_title_ar')->label('Subjects Column Title (Arabic)'),
                                            TextInput::make('footer_subjects_title_en')->label('Subjects Column Title (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('footer_contact_title_ar')->label('Contact Column Title (Arabic)'),
                                            TextInput::make('footer_contact_title_en')->label('Contact Column Title (English)'),
                                        ]),
                                    ]),

                                Section::make('Quick Links Repeater')
                                    ->schema([
                                        Repeater::make('footer_quick_links')
                                            ->label('Footer Quick Navigation Links')
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    TextInput::make('label_ar')->label('Link Label (Arabic)')->required(),
                                                    TextInput::make('label_en')->label('Link Label (English)')->required(),
                                                    TextInput::make('url')->label('Target Link URL / Route')->required(),
                                                ]),
                                            ])
                                            ->reorderable(true)
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Subjects / Services Links Repeater')
                                    ->schema([
                                        Repeater::make('footer_subjects_links')
                                            ->label('Footer Subjects & Services Links')
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    TextInput::make('label_ar')->label('Link Label (Arabic)')->required(),
                                                    TextInput::make('label_en')->label('Link Label (English)')->required(),
                                                    TextInput::make('url')->label('Target Link URL / Route')->required(),
                                                ]),
                                            ])
                                            ->reorderable(true)
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Footer Contact Details & Working Hours')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('contact_address_ar')->label('Address (Arabic)'),
                                            TextInput::make('contact_address_en')->label('Address (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('contact_phone')->label('Phone Number'),
                                            TextInput::make('contact_email')->label('Support Email'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('footer_working_hours_ar')->label('Working Hours (Arabic)'),
                                            TextInput::make('footer_working_hours_en')->label('Working Hours (English)'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('footer_rights_ar')->label('Copyright Notice (Arabic)'),
                                            TextInput::make('footer_rights_en')->label('Copyright Notice (English)'),
                                        ]),
                                    ]),

                                Section::make('Social Media Handles')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('social_facebook')->label('Facebook URL'),
                                            TextInput::make('social_twitter')->label('Twitter / X URL'),
                                            TextInput::make('social_instagram')->label('Instagram URL'),
                                            TextInput::make('social_linkedin')->label('LinkedIn URL'),
                                            TextInput::make('social_youtube')->label('YouTube URL'),
                                        ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Section Layout & Visibility')
                            ->icon('heroicon-o-arrows-up-down')
                            ->schema([
                                Section::make('🔀 Landing Page Layout & Section Visibility')
                                    ->schema([
                                        Repeater::make('sections_layout')
                                            ->label('Landing Page Sections Order & Visibility')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    Select::make('key')
                                                        ->label('Section Type')
                                                        ->options([
                                                            'hero-slider' => '1. Hero Banner Slider (سلايدر الرئيسية)',
                                                            'stats-overlay' => '2. Glass Statistics Bar (شريط الإحصائيات والأرقام)',
                                                            'why-choose' => '3. Why Choose Elite (لماذا أکاديمية إيليت)',
                                                            'about-preview' => '4. About Preview Section (عن الأكاديمية)',
                                                            'subjects-grid' => '5. Subjects Showcase Grid (المواد الدراسية)',
                                                            'teachers-marquee' => '6. Faculty Mentors (أعضاء هيئة التدريس)',
                                                            'testimonials' => '7. Student & Parent Reviews (آراء الطلاب وأولياء الأمور)',
                                                            'cta_section' => '8. Call to Action Banner (دعوة للانضمام والاشتراك)',
                                                        ])
                                                        ->required(),
                                                    Toggle::make('is_enabled')
                                                        ->label('Show Section on Landing Page')
                                                        ->default(true),
                                                ]),
                                            ])
                                            ->reorderable(true)
                                            ->cloneable(false)
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Theme & Branding Colors')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Visual Theme Customization')
                                    ->schema([
                                        ColorPicker::make('theme_primary_color')
                                            ->label('Primary Brand Accent Color')
                                            ->default('#0d9488'),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $k => $v) {
            if (is_array($v)) {
                SiteSetting::set($k, json_encode($v), 'landing');
            } else {
                SiteSetting::set($k, is_bool($v) ? ($v ? '1' : '0') : (string) $v, 'landing');
            }
        }

        Notification::make()
            ->title('Landing Page & CMS Settings Saved!')
            ->body('All landing page content, dynamic counters, section order, and footer settings updated successfully.')
            ->success()
            ->send();
    }
}