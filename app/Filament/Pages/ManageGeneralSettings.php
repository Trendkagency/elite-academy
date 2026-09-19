<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
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
use Illuminate\Support\HtmlString;

class ManageGeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 90;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'الإعدادات العامة' : 'Settings';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات المنصة والفوتر' : 'Platform & Footer Settings';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات المنصة والفوتر وروابط التواصل' : 'Platform, Footer & Social Settings';
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
            // Currency
            'currency_code' => SiteSetting::get('currency_code', 'EGP'),
            'currency_symbol_ar' => SiteSetting::get('currency_symbol_ar', 'ج.م'),
            'currency_symbol_en' => SiteSetting::get('currency_symbol_en', 'EGP'),
            'currency_position' => SiteSetting::get('currency_position', 'after'),

            // Contact & Info
            'contact_email' => SiteSetting::get('contact_email', 'contact@elite-academy.edu.eg'),
            'contact_phone' => SiteSetting::get('contact_phone', '+20 100 000 0000'),
            'contact_address_ar' => SiteSetting::get('contact_address_ar', 'برج الأكاديمية، القاهرة الجديدة، مصر'),
            'contact_address_en' => SiteSetting::get('contact_address_en', 'Academic Center Tower, New Cairo, Egypt'),
            'footer_working_hours_ar' => SiteSetting::get('footer_working_hours_ar', 'السبت - الخميس: 9:00 ص - 9:00 م'),
            'footer_working_hours_en' => SiteSetting::get('footer_working_hours_en', 'Sat - Thu: 9:00 AM - 9:00 PM'),
            'footer_rights_ar' => SiteSetting::get('footer_rights_ar', '© 2026 أكاديمية إيليت التعليمية. جميع الحقوق محفوظة.'),
            'footer_rights_en' => SiteSetting::get('footer_rights_en', '© 2026 Elite Academy. All rights reserved.'),

            // Footer CMS
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

            // Social Media Channels
            'social_facebook' => SiteSetting::get('social_facebook', 'https://facebook.com/eliteacademy'),
            'social_twitter' => SiteSetting::get('social_twitter', 'https://twitter.com/eliteacademy'),
            'social_instagram' => SiteSetting::get('social_instagram', 'https://instagram.com/eliteacademy'),
            'social_linkedin' => SiteSetting::get('social_linkedin', 'https://linkedin.com/company/eliteacademy'),
            'social_youtube' => SiteSetting::get('social_youtube', 'https://youtube.com/@eliteacademy'),
            'social_whatsapp' => SiteSetting::get('social_whatsapp', 'https://wa.me/201000000000'),
            'social_tiktok' => SiteSetting::get('social_tiktok', 'https://tiktok.com/@eliteacademy'),
            'social_telegram' => SiteSetting::get('social_telegram', 'https://t.me/eliteacademy'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';

        return $schema
            ->components([
                Tabs::make('SettingsTabs')
                    ->tabs([
                        // ── Tab 1: Social Media Links ──
                        Tabs\Tab::make('SocialMediaSettings')
                            ->label($isAr ? 'روابط التواصل الاجتماعي (السوشيال ميديا)' : 'Social Media Links')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make(new HtmlString('<i class="fa-solid fa-share-nodes text-teal-500 me-2"></i>' . ($isAr ? 'روابط حسابات التواصل المعروضة في الفوتر' : 'Footer Social Media Channels')))
                                    ->description($isAr
                                        ? 'قم بإدخال روابط حسابات المنصة الرسمية. الأيقونات ستظهر في الفوتر تلقائياً بأيقونات Font Awesome احترافية، وعند نقر الزائر سيتم توجيهه مباشرة للحساب.'
                                        : 'Enter official profile links. The icons will dynamically render in the footer using HTML Font Awesome brand icons.')
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

                        // ── Tab 3: Footer Content & Structure ──
                        Tabs\Tab::make('FooterContentSettings')
                            ->label($isAr ? 'إدارة محتوى وعناوين الفوتر' : 'Footer Content & Links')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                Section::make(new HtmlString('<i class="fa-solid fa-pen-to-square text-teal-500 me-2"></i>' . ($isAr ? 'نصوص وعناوين أعمدة الفوتر' : 'Footer Column Titles & Description')))
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

                                Section::make(new HtmlString('<i class="fa-solid fa-list-check text-teal-500 me-2"></i>' . ($isAr ? 'قائمة الروابط السريعة بالفوتر (Quick Links)' : 'Footer Quick Links Repeater')))
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

                                Section::make(new HtmlString('<i class="fa-solid fa-graduation-cap text-teal-500 me-2"></i>' . ($isAr ? 'قائمة روابط المسارات والمقررات (Tracks Links)' : 'Footer Tracks & Subjects Links')))
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

                        // ── Tab 4: Contact & Working Hours ──
                        Tabs\Tab::make('ContactInfoSettings')
                            ->label($isAr ? 'بيانات التواصل وساعات العمل' : 'Contact & Hours')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Section::make(new HtmlString('<i class="fa-solid fa-headset text-teal-500 me-2"></i>' . ($isAr ? 'بيانات الاتصال ومواعيد العمل وحقوق الملكية' : 'Contact, Hours & Copyright Details')))
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('contact_email')
                                                ->label($isAr ? 'البريد الإلكتروني الرسمي' : 'Official Contact Email')
                                                ->email(),

                                            TextInput::make('contact_phone')
                                                ->label($isAr ? 'رقم الهاتف / الخط الساخن' : 'Official Contact Phone')
                                                ->tel(),

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
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $k => $v) {
            if (is_array($v)) {
                SiteSetting::set($k, json_encode($v, JSON_UNESCAPED_UNICODE), 'general');
            } else {
                SiteSetting::set($k, (string) ($v ?? ''), 'general');
            }
        }

        Notification::make()
            ->title(app()->getLocale() === 'ar' ? 'تم حفظ كافة الإعدادات والفوتر بنجاح!' : 'Platform & Footer Settings Saved!')
            ->body(app()->getLocale() === 'ar'
                ? 'تم تحديث روابط السوشيال ميديا وعناصر الفوتر بنجاح.'
                : 'Footer social links and navigation menus updated successfully.')
            ->success()
            ->send();
    }
}

