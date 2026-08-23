<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageContactPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-phone';

    protected static \UnitEnum|string|null $navigationGroup = 'CMS Management';

    protected static ?string $navigationLabel = 'Contact Page CMS & Live Preview';

    protected static ?string $title = 'Manage Contact Page Content & iFrame Live Preview';

    protected string $view = 'filament.pages.manage-contact-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'contact_hero_badge' => SiteSetting::get('contact_hero_badge', 'STUDENT & PARENT SUPPORT'),
            'contact_hero_title' => SiteSetting::get('contact_hero_title', 'We Are Always Here To Help'),
            'contact_hero_subtitle' => SiteSetting::get('contact_hero_subtitle', 'Have questions regarding curriculum enrollment, parent progress dashboards, or scheduling a campus visit? Reach out to our dedicated support advisors.'),
            'contact_hero_image' => SiteSetting::get('contact_hero_image', 'images/academy_campus.png'),
            'contact_card_title' => SiteSetting::get('contact_card_title', 'Support Desk 24/7'),
            'contact_card_subtitle' => SiteSetting::get('contact_card_subtitle', 'Direct Academic Assistance'),
            'contact_card_icon' => SiteSetting::get('contact_card_icon', '🎧'),
            'contact_phone' => SiteSetting::get('contact_phone', '+20 100 123 4567'),
            'contact_whatsapp' => SiteSetting::get('contact_whatsapp', '+20 100 123 4568'),
            'owner_whatsapp' => SiteSetting::get('owner_whatsapp', '+20 100 000 0000'),
            'contact_email' => SiteSetting::get('contact_email', 'support@elite-academy.edu.eg'),
            'contact_address' => SiteSetting::get('contact_address', 'New Cairo Hub, Egypt'),
            'contact_form_title' => SiteSetting::get('contact_form_title', 'Send Us a Message'),
            'contact_form_subtitle' => SiteSetting::get('contact_form_subtitle', 'Our student advisors will respond within 24 hours.'),
            'contact_map_iframe_url' => SiteSetting::get('contact_map_iframe_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d55251.33660578643!2d31.470000000000002!3d30.030000000000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145822ee00000001%3A0x1000000000000000!2sNew%20Cairo%2C%20Cairo%20Governorate%2C%20Egypt!5e0!3m2!1sen!2seg!4v1700000000000!5m2!1sen!2seg'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hero & Heading Content')
                    ->components([
                        TextInput::make('contact_hero_badge')
                            ->label('Hero Badge Text')
                            ->required(),
                        TextInput::make('contact_hero_title')
                            ->label('Hero Title')
                            ->required(),
                        Textarea::make('contact_hero_subtitle')
                            ->label('Hero Subtitle / Description')
                            ->rows(3)
                            ->required(),
                    ]),

                Section::make('Contact Hero Media & Campus Image')
                    ->components([
                        FileUpload::make('contact_hero_image')
                            ->label('Contact Page Campus / Support Image (Drag & Drop)')
                            ->disk('public')
                            ->directory('cms')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->helperText('Upload custom image to display on the contact page hero section.'),
                    ]),

                Section::make('Support Desk Floating Badge (كارت الدعم والمساعدة المباشرة)')
                    ->columns(3)
                    ->components([
                        TextInput::make('contact_card_title')
                            ->label('Support Desk Badge Title')
                            ->helperText('e.g. Support Desk 24/7')
                            ->required(),
                        TextInput::make('contact_card_subtitle')
                            ->label('Support Desk Subtitle')
                            ->helperText('e.g. Direct Academic Assistance')
                            ->required(),
                        TextInput::make('contact_card_icon')
                            ->label('Badge Emoji / Icon')
                            ->helperText('e.g. 🎧')
                            ->required(),
                    ]),

                Section::make('Contact Details & Payment WhatsApp Phone Numbers')
                    ->columns(2)
                    ->components([
                        TextInput::make('contact_phone')
                            ->label('Phone Number Support')
                            ->required(),
                        TextInput::make('contact_whatsapp')
                            ->label('General Support WhatsApp')
                            ->required(),
                        TextInput::make('owner_whatsapp')
                            ->label('Owner Payment & Package Renewal WhatsApp (رقم واتساب الدفع واشتراك الباقات)')
                            ->helperText('This WhatsApp phone number is used across parent portal payment buttons and renewal inquiries.')
                            ->required(),
                        TextInput::make('contact_email')
                            ->label('Support Email Address')
                            ->email()
                            ->required(),
                        TextInput::make('contact_address')
                            ->label('Campus Location / Address')
                            ->required(),
                    ]),

                Section::make('Contact Form Section (عنوان وصف نموذج التراسل)')
                    ->columns(2)
                    ->components([
                        TextInput::make('contact_form_title')
                            ->label('Form Title')
                            ->required(),
                        TextInput::make('contact_form_subtitle')
                            ->label('Form Subtitle')
                            ->required(),
                    ]),

                Section::make('Google Maps iFrame Embed')
                    ->components([
                        Textarea::make('contact_map_iframe_url')
                            ->label('Google Maps Embed URL')
                            ->rows(2)
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value, 'contact');
        }

        Notification::make()
            ->title('Contact Page CMS Settings Saved Successfully!')
            ->success()
            ->send();
    }
}
