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
            'theme_secondary_color',
            'theme_accent_color',
            'theme_surface_mode',
            'theme_btn_radius',
            'theme_btn_style',
            'theme_btn_hover',
            'theme_btn_font_weight',
            'theme_card_radius',
            'theme_card_style',
            'theme_card_shadow',
            'theme_card_hover',
            'theme_badge_radius',
            'theme_badge_style',
            'theme_slider_overlay_opacity',
            'theme_slider_indicator_style',
            'theme_slider_kenburns',
            'theme_nav_style',
            'theme_nav_blur',
            'theme_font_family_en',
            'theme_font_family_ar',
            'currency_code',
            'currency_symbol_en',
            'currency_symbol_ar',
            'currency_position',
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
            'theme_secondary_color' => '#6366f1',
            'theme_accent_color' => '#f59e0b',
            'theme_surface_mode' => 'dark-glass',
            'theme_btn_radius' => 'full',
            'theme_btn_style' => 'gradient',
            'theme_btn_hover' => 'lift-glow',
            'theme_btn_font_weight' => 'bold',
            'theme_card_radius' => '2xl',
            'theme_card_style' => 'glass',
            'theme_card_shadow' => 'glow-soft',
            'theme_card_hover' => 'lift',
            'theme_badge_radius' => 'full',
            'theme_badge_style' => 'glass-glow',
            'theme_slider_overlay_opacity' => '55',
            'theme_slider_indicator_style' => 'dynamic-pill',
            'theme_slider_kenburns' => '1',
            'theme_nav_style' => 'glass-sticky',
            'theme_nav_blur' => 'md',
            'theme_font_family_en' => 'Cairo',
            'theme_font_family_ar' => 'Cairo',
            'currency_code' => 'EGP',
            'currency_symbol_en' => 'EGP',
            'currency_symbol_ar' => 'ج.م',
            'currency_position' => 'after',
            default => '',
        };
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('SettingsTabs')
                    ->tabs([
                        Tabs\Tab::make(app()->getLocale() === 'ar' ? 'شرائح الهيرو' : 'Hero Slides')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-film text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'سلايدر الهيرو الرئيسي — يدار عبر شرائح الهيرو' : 'Hero Slider — Managed via Hero Slides')))
                                    ->description(app()->getLocale() === 'ar' ? 'سلايدر الهيرو في الصفحة الرئيسية الآن ديناميكي بالكامل. كل شريحة لها صورتها، عنوانها، شارتها، أزرارها، وموضعها.' : 'The hero slider on the landing page is now fully dynamic. Each slide has its own image, headline, badge, CTA buttons, accent color, overlay, and position.')
                                    ->schema([
                                        \Filament\Schemas\Components\Html::make(new \Illuminate\Support\HtmlString(
                                            '<div style="border:1.5px solid rgba(45,212,191,0.3); background:linear-gradient(145deg, rgba(20,184,166,0.08) 0%, rgba(15,23,42,0.2) 100%); border-radius:16px; padding:36px 28px; text-align:center;">'
                                            . '<div style="display:inline-flex; align-items:center; justify-content:center; width:82px; height:82px; border-radius:22px; background:linear-gradient(135deg, rgba(20,184,166,0.2) 0%, rgba(13,148,136,0.35) 100%); border:2px solid rgba(45,212,191,0.4); color:#2dd4bf; font-size:36px; margin-bottom:18px; box-shadow:0 12px 28px -6px rgba(20,184,166,0.35);">'
                                            . '<i class="fa-solid fa-photo-film"></i>'
                                            . '</div>'
                                            . '<h3 style="font-size:18px; font-weight:800; color:#2dd4bf; margin:0 0 10px; letter-spacing:-0.01em;">' . (app()->getLocale() === 'ar' ? 'قسم إدارة شرائح الهيرو' : 'Hero Slides Resource') . '</h3>'
                                            . '<p style="color:#94a3b8; font-size:14px; max-width:520px; margin:0 auto 10px; line-height:1.7;">' . (app()->getLocale() === 'ar' ? 'يمكنك إنشاء، تعديل، <strong style="color:#e2e8f0;">إعادة ترتيب (بالسحب والإفلات)</strong>، والتحكم في ظهور الشرائح مباشرة من قسم <strong style="color:#e2e8f0;">Hero Slides</strong> في القائمة الجانبية.' : 'Create, edit, <strong style="color:#e2e8f0;">reorder (drag &amp; drop)</strong>, and toggle visibility of slides directly from the <strong style="color:#e2e8f0;">Hero Slides</strong> section in the sidebar under <em>Landing Page CMS</em>.') . '</p>'
                                            . '<p style="color:#64748b; font-size:12.5px; margin:0 auto 22px; max-width:560px; line-height:1.6;">' . (app()->getLocale() === 'ar' ? 'تدعم كل شريحة: صورة الخلفية، نص الشارة والأيقونة، العنوان الرئيسي والفرعي، أزرار الدعوة لاتخاذ إجراء، تدرجات الألوان، شفافية الغطاء، ومحاذاة المحتوى.' : 'Each slide supports: background image, badge label &amp; icon, headline, subtitle, primary &amp; secondary CTA buttons (with custom labels), accent color palette (teal / purple / orange / rose / sky / amber), overlay opacity, text alignment, and a 9-point content position grid.') . '</p>'
                                            . '<a href="' . (class_exists(\App\Filament\Resources\HeroSlides\HeroSlideResource::class) ? \App\Filament\Resources\HeroSlides\HeroSlideResource::getUrl('index') : url('/admin/hero-slides')) . '" style="display:inline-flex; align-items:center; gap:10px; padding:12px 26px; border-radius:10px; background:linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); color:#ffffff; font-weight:700; font-size:14px; text-decoration:none; box-shadow:0 6px 20px -2px rgba(20,184,166,0.4); transition:all 0.2s ease;">'
                                            . '<i class="fa-solid fa-sliders"></i>'
                                            . '<span>' . (app()->getLocale() === 'ar' ? 'إدارة شرائح الهيرو (Hero Slides)' : 'Manage Hero Slides') . '</span>'
                                            . '<i class="fa-solid ' . (app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right') . '"></i>'
                                            . '</a>'
                                            . '</div>'
                                        )),
                                    ]),
                            ]),


                        Tabs\Tab::make('Statistics & Metrics')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-chart-pie text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'إحصائيات المنصة وأرقام الإنجاز' : 'Statistics Counters & Dynamic Metrics')))
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
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-layer-group text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'ترتيب الأقسام والظهور' : 'Landing Page Layout & Section Visibility')))
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
                                // ── LIVE INTERACTIVE COMPONENT PREVIEW ──
                                \Filament\Schemas\Components\View::make('filament.components.theme-preview')
                                    ->columnSpanFull(),

                                // ── 1. BRAND COLORS & SURFACE ──
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-palette text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'لوحة ألوان الهوية والمظهر' : 'Brand Color Palette & Surface Mode')))
                                    ->description('Customize core brand colors, secondary accents, and overall visual mode.')
                                    ->columns(2)
                                    ->schema([
                                        ColorPicker::make('theme_primary_color')
                                            ->label('Primary Brand Color')
                                            ->default('#0d9488')
                                            ->required(),

                                        ColorPicker::make('theme_secondary_color')
                                            ->label('Secondary Brand Accent')
                                            ->default('#6366f1')
                                            ->required(),

                                        ColorPicker::make('theme_accent_color')
                                            ->label('Highlight & Badge Glow Accent')
                                            ->default('#f59e0b')
                                            ->required(),

                                        Select::make('theme_surface_mode')
                                            ->label('Global Surface Aesthetic')
                                            ->options([
                                                'dark-glass'  => 'Dark Futuristic Glass (Default)',
                                                'deep-slate'  => 'Deep Slate Modern',
                                                'pure-dark'   => 'Pure OLED Black',
                                                'clean-light' => 'Clean Light Surface',
                                            ])
                                            ->default('dark-glass')
                                            ->native(false),
                                    ]),

                                // ── 2. BUTTONS & CTA ELEMENTS ──
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-toggle-on text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'أنماط أزرار الإجراء (Buttons & CTA)' : 'Button & CTA Styles')))
                                    ->description('Global button curvature, gradient effects, hover animations, and typography.')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('theme_btn_radius')
                                            ->label('Button Corner Radius')
                                            ->options([
                                                'full' => 'Full Pill (9999px)',
                                                'lg'   => 'Large Rounded (12px)',
                                                'md'   => 'Medium Rounded (8px)',
                                                'sm'   => 'Small Rounded (4px)',
                                                'none' => 'Sharp Square (0px)',
                                            ])
                                            ->default('full')
                                            ->native(false),

                                        Select::make('theme_btn_style')
                                            ->label('Button Visual Variant')
                                            ->options([
                                                'gradient' => 'Radiant Dual Gradient',
                                                'solid'    => 'Solid Bold Color',
                                                'glow'     => 'Neon Ambient Glow',
                                                'glass'    => 'Translucent Glass',
                                            ])
                                            ->default('gradient')
                                            ->native(false),

                                        Select::make('theme_btn_hover')
                                            ->label('Hover Interaction Effect')
                                            ->options([
                                                'lift-glow' => 'Float Up + Expand Glow',
                                                'lift'      => 'Smooth Float Up',
                                                'glow'      => 'Aura Glow Intensify',
                                                'scale'     => 'Subtle Scale Pop',
                                            ])
                                            ->default('lift-glow')
                                            ->native(false),

                                        Select::make('theme_btn_font_weight')
                                            ->label('Button Font Weight')
                                            ->options([
                                                'extrabold' => 'Extra Bold (800)',
                                                'bold'      => 'Bold (700)',
                                                'semibold'  => 'Semi Bold (600)',
                                                'medium'    => 'Medium (500)',
                                            ])
                                            ->default('bold')
                                            ->native(false),
                                    ]),

                                // ── 3. CARDS & CONTAINERS ──
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-table-cells-large text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'البطاقات والحاويات والتأثير الزجاجي' : 'Cards, Containers & Glassmorphism')))
                                    ->description('Configure shape, background styling, shadow depth, and float animations for all cards.')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('theme_card_radius')
                                            ->label('Card Corner Radius')
                                            ->options([
                                                '3xl' => 'Extra Large Rounded (24px)',
                                                '2xl' => 'Large Rounded (16px)',
                                                'xl'  => 'Medium-Large (12px)',
                                                'lg'  => 'Standard (8px)',
                                            ])
                                            ->default('2xl')
                                            ->native(false),

                                        Select::make('theme_card_style')
                                            ->label('Card Surface Style')
                                            ->options([
                                                'glass'    => 'Translucent Frosted Glass',
                                                'solid'    => 'Solid Deep Slate',
                                                'bordered' => 'Highlighted Border Glow',
                                                'elevated' => '3D Elevated Layer',
                                            ])
                                            ->default('glass')
                                            ->native(false),

                                        Select::make('theme_card_shadow')
                                            ->label('Card Shadow & Glow Depth')
                                            ->options([
                                                'glow-soft' => 'Soft Ambient Accent Glow',
                                                'lg'        => 'Deep 3D Shadow',
                                                'md'        => 'Medium Soft Shadow',
                                                'none'      => 'Flat (No Shadow)',
                                            ])
                                            ->default('glow-soft')
                                            ->native(false),

                                        Select::make('theme_card_hover')
                                            ->label('Card Hover Animation')
                                            ->options([
                                                'lift'  => 'Float Up Smoothly',
                                                'glow'  => 'Accent Border Illuminates',
                                                'scale' => 'Subtle Scale',
                                                'none'  => 'Static',
                                            ])
                                            ->default('lift')
                                            ->native(false),
                                    ]),

                                // ── 4. BADGES & STATUS TAGS ──
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-tag text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'الشارات والشريط الترويجي' : 'Badges & Indicator Tags')))
                                    ->description('Appearance of category badges, rating badges, and live indicator tags.')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('theme_badge_radius')
                                            ->label('Badge Corner Radius')
                                            ->options([
                                                'full' => 'Pill Shape (9999px)',
                                                'md'   => 'Rounded (8px)',
                                                'sm'   => 'Compact (4px)',
                                            ])
                                            ->default('full')
                                            ->native(false),

                                        Select::make('theme_badge_style')
                                            ->label('Badge Visual Style')
                                            ->options([
                                                'glass-glow' => 'Glass + Ambient Glow',
                                                'solid'      => 'Solid Accent Pill',
                                                'outline'    => 'Crisp Outline Border',
                                                'minimal'    => 'Subtle Tint',
                                            ])
                                            ->default('glass-glow')
                                            ->native(false),
                                    ]),

                                // ── 5. HERO SLIDER VISUALS ──
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-images text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'إعدادات شرائح وبانرات الهيرو' : 'Hero Slider & Banners Defaults')))
                                    ->description('Default overlay darkness and indicator behavior for the landing page hero slider.')
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('theme_slider_overlay_opacity')
                                            ->label('Default Overlay Darkness (0–100)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->default(55)
                                            ->suffix('%'),

                                        Select::make('theme_slider_indicator_style')
                                            ->label('Slide Indicator Style')
                                            ->options([
                                                'dynamic-pill' => 'Dynamic Elongating Bar',
                                                'dots'         => 'Classic Dots',
                                                'numbers'      => '01/04 Numeric Counter',
                                            ])
                                            ->default('dynamic-pill')
                                            ->native(false),

                                        Toggle::make('theme_slider_kenburns')
                                            ->label('Ken-Burns Zoom Animation')
                                            ->default(true)
                                            ->inline(false),
                                    ]),

                                // ── 6. HEADER & NAVIGATION ──
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-compass text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'شريط التنقل العلوي (Header & Nav)' : 'Header & Navigation Bar')))
                                    ->description('Styling and blur depth for the top navigation bar.')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('theme_nav_style')
                                            ->label('Navbar Layout Style')
                                            ->options([
                                                'glass-sticky'  => 'Sticky Frosted Glass (Default)',
                                                'solid'         => 'Solid Deep Navbar',
                                                'floating-pill' => 'Floating Island Pill',
                                            ])
                                            ->default('glass-sticky')
                                            ->native(false),

                                        Select::make('theme_nav_blur')
                                            ->label('Backdrop Blur Strength')
                                            ->options([
                                                'lg' => 'Heavy Blur (20px)',
                                                'md' => 'Medium Blur (12px)',
                                                'sm' => 'Light Blur (6px)',
                                            ])
                                            ->default('md')
                                            ->native(false),
                                    ]),

                                // ── 7. TYPOGRAPHY & FONTS ──
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-font text-teal-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'الخطوط والتايبوجرافي' : 'Typography & Font Families')))
                                    ->description('Curated Google Fonts for headlines, body text, and multilingual rendering.')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('theme_font_family_en')
                                            ->label('English Font Family')
                                            ->options([
                                                'Cairo'             => 'Cairo (Default)',
                                                'Inter'             => 'Inter (Clean & Modern)',
                                                'Outfit'            => 'Outfit (Tech & Premium)',
                                                'Plus Jakarta Sans' => 'Plus Jakarta Sans (Editorial)',
                                                'Poppins'           => 'Poppins (Geometric & Bold)',
                                                'Roboto'            => 'Roboto (Standard)',
                                            ])
                                            ->default('Cairo')
                                            ->native(false),

                                        Select::make('theme_font_family_ar')
                                            ->label('Arabic Font Family')
                                            ->options([
                                                'Cairo'                => 'Cairo (الافتراضي)',
                                                'Alexandria'           => 'Alexandria (الاسكندرية)',
                                                'Tajawal'              => 'Tajawal (تجوال)',
                                                'Almarai'              => 'Almarai (المراعي)',
                                                'IBM Plex Sans Arabic' => 'IBM Plex Sans Arabic',
                                            ])
                                            ->default('Cairo')
                                            ->native(false),
                                    ]),
                            ]),

                        Tabs\Tab::make(app()->getLocale() === 'ar' ? 'العملة والأسعار' : 'Currency & Pricing')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make(new \Illuminate\Support\HtmlString('<i class="fa-solid fa-coins text-amber-400 me-2"></i>' . (app()->getLocale() === 'ar' ? 'إعدادات العملة والأسعار بالنظام' : 'System Currency & Pricing Configuration')))
                                    ->description(app()->getLocale() === 'ar' 
                                        ? 'التحكم في العملة الافتراضية للنظام (الافتراضي: الجنيه المصري EGP / ج.م) وطريقة عرض الأسعار في كافة أجزاء الموقع واللوحة.' 
                                        : 'Control default platform currency (Default: Egyptian Pound EGP / ج.م) and formatting across all courses, packages, and reports.')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Select::make('currency_code')
                                                ->label(app()->getLocale() === 'ar' ? 'رمز العملة القياسي (ISO Code)' : 'Currency Code (ISO)')
                                                ->options([
                                                    'EGP' => 'EGP — Egyptian Pound (الجنيه المصري)',
                                                    'USD' => 'USD — US Dollar (الدولار الأمريكي)',
                                                    'SAR' => 'SAR — Saudi Riyal (الريال السعودي)',
                                                    'AED' => 'AED — UAE Dirham (الدرهم الإماراتي)',
                                                    'KWD' => 'KWD — Kuwaiti Dinar (الدينار الكويتي)',
                                                    'QAR' => 'QAR — Qatari Riyal (الريال القطري)',
                                                    'EUR' => 'EUR — Euro (اليورو الأوروبي)',
                                                    'GBP' => 'GBP — British Pound (الجنيه الإسترليني)',
                                                ])
                                                ->default('EGP')
                                                ->required()
                                                ->native(false),

                                            TextInput::make('currency_symbol_ar')
                                                ->label(app()->getLocale() === 'ar' ? 'رمز العملة (بالعربية)' : 'Currency Symbol (Arabic)')
                                                ->default('ج.م')
                                                ->placeholder('ج.م')
                                                ->required(),

                                            TextInput::make('currency_symbol_en')
                                                ->label(app()->getLocale() === 'ar' ? 'رمز العملة (بالإنجليزية)' : 'Currency Symbol (English)')
                                                ->default('EGP')
                                                ->placeholder('EGP')
                                                ->required(),
                                        ]),

                                        Grid::make(2)->schema([
                                            Select::make('currency_position')
                                                ->label(app()->getLocale() === 'ar' ? 'موضع رمز العملة' : 'Symbol Position')
                                                ->options([
                                                    'after'  => app()->getLocale() === 'ar' ? 'بعد المبلغ (مثال: 290 ج.م / 290 EGP)' : 'After Amount (e.g. 290 EGP / 290 ج.م)',
                                                    'before' => app()->getLocale() === 'ar' ? 'قبل المبلغ (مثال: ج.م 290 / EGP 290)' : 'Before Amount (e.g. EGP 290 / $ 290)',
                                                ])
                                                ->default('after')
                                                ->required()
                                                ->native(false),
                                        ]),

                                        \Filament\Schemas\Components\Html::make(new \Illuminate\Support\HtmlString(
                                            '<div style="background: rgba(20, 184, 166, 0.08); border: 1px solid rgba(20, 184, 166, 0.25); border-radius: 1rem; padding: 1.25rem; margin-top: 0.5rem;">'
                                            . '<div style="font-weight: 800; font-size: 0.85rem; color: #0D9488; margin-bottom: 0.35rem;"><i class="fa-solid fa-wand-magic-sparkles text-teal-500 me-2"></i>' . (app()->getLocale() === 'ar' ? 'معاينة فورية لعرض الأسعار:' : 'Live Currency Display Preview:') . '</div>'
                                            . '<div style="display: flex; gap: 1rem; font-family: monospace; font-size: 1.1rem; font-weight: 800; color: #0F172A;" class="dark:text-white">'
                                            . '<span>' . (app()->getLocale() === 'ar' ? 'بالعربية: ' : 'Arabic: ') . '<span style="color: #0D9488;">' . format_currency(290, 'ar') . '</span></span>'
                                            . '<span> | </span>'
                                            . '<span>' . (app()->getLocale() === 'ar' ? 'بالإنجليزية: ' : 'English: ') . '<span style="color: #0D9488;">' . format_currency(290, 'en') . '</span></span>'
                                            . '</div>'
                                            . '</div>'
                                        )),
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