<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageAboutPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-information-circle';

    protected static \UnitEnum|string|null $navigationGroup = 'CMS Management';

    protected static ?string $navigationLabel = 'About Page CMS & Live Preview';

    protected static ?string $title = 'Manage About Page Content & iFrame Live Preview';

    protected string $view = 'filament.pages.manage-about-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'about_hero_badge' => SiteSetting::get('about_hero_badge', 'ACCREDITED EXCELLENCE • EST. 2020'),
            'about_hero_title' => SiteSetting::get('about_hero_title', 'Transforming Academic Education For Future Leaders'),
            'about_hero_subtitle' => SiteSetting::get('about_hero_subtitle', 'Elite Academy combines accredited national curricula with interactive video sessions, real-time mentor Q&A, and parent progress tracking.'),
            'about_mission_title' => SiteSetting::get('about_mission_title', 'Our Core Educational Mission'),
            'about_mission_text' => SiteSetting::get('about_mission_text', 'To deliver accessible, high-quality, and interactive learning experiences that empower secondary students to excel in national and international examinations.'),
            'about_vision_title' => SiteSetting::get('about_vision_title', 'Our Vision For Tomorrow'),
            'about_vision_text' => SiteSetting::get('about_vision_text', 'Empowering 100,000+ students across Egypt and the MENA region with personalized academic paths, expert faculty mentorship, and innovative digital learning tools.'),
            'about_stat_students' => SiteSetting::get('about_stat_students', '25,000+'),
            'about_stat_courses' => SiteSetting::get('about_stat_courses', '120+'),
            'about_stat_teachers' => SiteSetting::get('about_stat_teachers', '45+'),
            'about_stat_pass_rate' => SiteSetting::get('about_stat_pass_rate', '98.5%'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hero Header Content')
                    ->components([
                        TextInput::make('about_hero_badge')
                            ->label('Hero Badge Text')
                            ->required(),
                        TextInput::make('about_hero_title')
                            ->label('Hero Title')
                            ->required(),
                        Textarea::make('about_hero_subtitle')
                            ->label('Hero Subtitle / Description')
                            ->rows(3)
                            ->required(),
                    ]),

                Section::make('Mission & Vision Sections')
                    ->columns(2)
                    ->components([
                        TextInput::make('about_mission_title')
                            ->label('Mission Title')
                            ->required(),
                        Textarea::make('about_mission_text')
                            ->label('Mission Text')
                            ->rows(3)
                            ->required(),
                        TextInput::make('about_vision_title')
                            ->label('Vision Title')
                            ->required(),
                        Textarea::make('about_vision_text')
                            ->label('Vision Text')
                            ->rows(3)
                            ->required(),
                    ]),

                Section::make('Academy Statistics')
                    ->columns(2)
                    ->components([
                        TextInput::make('about_stat_students')
                            ->label('Active Students Count')
                            ->required(),
                        TextInput::make('about_stat_courses')
                            ->label('Accredited Courses Count')
                            ->required(),
                        TextInput::make('about_stat_teachers')
                            ->label('Expert Faculty Count')
                            ->required(),
                        TextInput::make('about_stat_pass_rate')
                            ->label('Exam Success Pass Rate')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value, 'about');
        }

        Notification::make()
            ->title('About Page CMS Settings Saved Successfully!')
            ->success()
            ->send();
    }
}
