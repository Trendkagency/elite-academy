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
        return app()->getLocale() === 'ar' ? 'إعدادات الرئيسية والهوية' : 'Landing Page & Theme';
    }

    protected string $view = 'filament.pages.manage-landing-page';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'landing_hero_badge',
            'landing_hero_title',
            'landing_hero_subtitle',
            'landing_cta_primary_text',
            'landing_cta_primary_link',
            'announcement_enabled',
            'announcement_text',
            'announcement_link',
            'about_badge',
            'about_title',
            'about_content',
            'about_image',
            'cta_headline',
            'cta_subtitle',
            'cta_bg_image',
            'contact_phone',
            'contact_email',
            'contact_address',
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
            'landing_hero_badge' => '🚀 EGYPT’S #1 ACADEMIC PLATFORM',
            'landing_hero_title' => 'Empowering Future Leaders with Practical Academic Excellence',
            'landing_hero_subtitle' => 'Join thousands of students learning Programming, Artificial Intelligence, Science, and Business from Egypt’s top educators.',
            'landing_cta_primary_text' => 'Explore All Subjects →',
            'landing_cta_primary_link' => '/subjects',
            'announcement_enabled' => '1',
            'announcement_text' => '🎉 Fall Cohort 2026 Registration is Now Open! Enroll in Live Stream Sessions.',
            'announcement_link' => '/courses',
            'about_badge' => 'REDEFINING EDUCATION',
            'about_title' => 'Where Passion Meets Academic Mastery',
            'about_content' => 'Elite Academy bridges secondary education and real-world innovation through interactive live streams, structured MCQs, and expert teacher mentorship.',
            'cta_headline' => 'Ready to Excel in Your Academic Journey?',
            'cta_subtitle' => 'Join Elite Academy today and gain unlimited access to top teachers, interactive live streams, and accredited courses.',
            'contact_phone' => '+20 100 000 0000',
            'contact_email' => 'info@eliteacademy.edu.eg',
            'contact_address' => 'New Cairo, Egypt',
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
                        Tabs\Tab::make('Hero & Branding')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make('Main Hero Banner Settings')
                                    ->description('Customize the main hero title, badge, subheadline, and call to action.')
                                    ->schema([
                                        TextInput::make('landing_hero_badge')
                                            ->label('Hero Top Badge Text')
                                            ->placeholder('e.g. 🚀 EGYPT’S #1 ACADEMIC PLATFORM'),
                                        TextInput::make('landing_hero_title')
                                            ->label('Main Hero Title / Headline')
                                            ->required(),
                                        Textarea::make('landing_hero_subtitle')
                                            ->label('Hero Subtitle / Description')
                                            ->rows(3),
                                        Grid::make(2)->schema([
                                            TextInput::make('landing_cta_primary_text')
                                                ->label('Primary Button Text')
                                                ->placeholder('Explore All Subjects →'),
                                            TextInput::make('landing_cta_primary_link')
                                                ->label('Primary Button URL')
                                                ->placeholder('/subjects'),
                                        ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Announcement Bar')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Section::make('Top Notification Banner')
                                    ->description('Manage top notification bar across the website.')
                                    ->schema([
                                        Toggle::make('announcement_enabled')
                                            ->label('Enable Top Announcement Banner'),
                                        TextInput::make('announcement_text')
                                            ->label('Banner Text Message'),
                                        TextInput::make('announcement_link')
                                            ->label('Banner Click Action Link'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Section Order & Swapper')
                            ->icon('heroicon-o-arrows-up-down')
                            ->schema([
                                Section::make('🔀 Landing Page Layout & Section Visibility')
                                    ->description('Drag & drop or reorder landing page sections to instantly change the page layout and swap sections.')
                                    ->schema([
                                        Repeater::make('sections_layout')
                                            ->label('Landing Page Sections Order')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    Select::make('key')
                                                        ->label('Section Type')
                                                        ->options([
                                                            'hero-slider' => '1. Hero Banner Slider (سلايدر الرئيسية)',
                                                            'stats-overlay' => '2. Glass Statistics Bar (شريط الإحصائيات)',
                                                            'why-choose' => '3. Why Choose Elite (لماذا أکاديمية إيليت)',
                                                            'about-preview' => '4. About Preview Section (عن الأكاديمية)',
                                                            'subjects-grid' => '5. Subjects Showcase Grid (المواد الدراسية)',
                                                            'teachers-marquee' => '6. Faculty Mentors (أعضاء هيئة التدريس)',
                                                            'testimonials' => '7. Student & Parent Reviews (آراء الطلاب وأولياء الأمور)',
                                                            'cta_section' => '8. Call to Action Banner (دعوة للانضمام)',
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

                        Tabs\Tab::make('About Section')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('About Academy Preview')
                                    ->schema([
                                        TextInput::make('about_badge')
                                            ->label('Badge Text'),
                                        TextInput::make('about_title')
                                            ->label('Section Title'),
                                        Textarea::make('about_content')
                                            ->label('Section Overview Content')
                                            ->rows(4),
                                        FileUpload::make('about_image')
                                            ->label('About Section Photo (Drag & Drop)')
                                            ->disk('public')
                                            ->directory('landing-page')
                                            ->image(),
                                    ]),
                            ]),

                        Tabs\Tab::make('CTA & Footer Settings')
                            ->icon('heroicon-o-paper-airplane')
                            ->schema([
                                Section::make('Call to Action Banner')
                                    ->schema([
                                        TextInput::make('cta_headline')
                                            ->label('CTA Title'),
                                        Textarea::make('cta_subtitle')
                                            ->label('CTA Subtitle'),
                                        FileUpload::make('cta_bg_image')
                                            ->label('CTA Background Image (Drag & Drop)')
                                            ->disk('public')
                                            ->directory('landing-page')
                                            ->image(),
                                    ]),
                                Section::make('Footer Contact Info & Social Media Links')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('contact_phone')->label('Phone Number'),
                                            TextInput::make('contact_email')->label('Support Email'),
                                            TextInput::make('contact_address')->label('Address / Location'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('social_facebook')->label('Facebook URL'),
                                            TextInput::make('social_twitter')->label('Twitter / X URL'),
                                            TextInput::make('social_instagram')->label('Instagram URL'),
                                            TextInput::make('social_linkedin')->label('LinkedIn URL'),
                                            TextInput::make('social_youtube')->label('YouTube URL'),
                                        ]),
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
            ->title('Landing Page & Layout Settings Updated!')
            ->body('Section layout order, drag & drop images, and theme configurations have been saved successfully.')
            ->success()
            ->send();
    }
}