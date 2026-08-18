<?php

namespace App\Filament\Pages;

use App\Services\Notification\FcmNotificationService;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SendFcmBroadcastPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static \UnitEnum|string|null $navigationGroup = 'CMS & Communications';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Custom FCM Push Broadcast';

    protected static ?string $title = 'Custom FCM Push Broadcast & Target Audience Dispatcher';

    protected string $view = 'filament.pages.send-fcm-broadcast-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'target_audience' => 'students',
            'title' => '📢 Important Announcement from Administration',
            'body' => '',
            'action_url' => route('student-portal'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Broadcast Target Audience & Notification Content')
                    ->components([
                        Radio::make('target_audience')
                            ->label('Target Audience Group')
                            ->options([
                                'students' => '🎓 Students Only (Enrolled Learners)',
                                'teachers' => '👨‍🏫 Teachers Only (Instructors & Faculty)',
                                'parents' => '👪 Parents Only (Guardians & Sponsors)',
                                'all' => '🌍 All Registered System Users (Full Broadcast)',
                            ])
                            ->default('students')
                            ->required(),

                        TextInput::make('title')
                            ->label('Notification Title')
                            ->placeholder('e.g. 📢 Important Notice: Upcoming Exam Schedule Update!')
                            ->required(),

                        Textarea::make('body')
                            ->label('Notification Message Body')
                            ->placeholder('e.g. Dear students, please review your dashboard for session schedule updates.')
                            ->rows(4)
                            ->required(),

                        TextInput::make('action_url')
                            ->label('Click Action / Target URL (Optional)')
                            ->placeholder('https://...')
                            ->nullable(),
                    ]),
            ])
            ->statePath('data');
    }

    public function sendBroadcast(): void
    {
        $data = $this->form->getState();

        $service = app(FcmNotificationService::class);
        $count = $service->broadcastNotification(
            $data['target_audience'],
            $data['title'],
            $data['body'],
            $data['action_url'] ?? null
        );

        Notification::make()
            ->title('FCM Push Broadcast Dispatched Successfully!')
            ->body("Dispatched real-time push & system notifications to {$count} targeted user(s).")
            ->success()
            ->send();

        $this->form->fill([
            'target_audience' => 'students',
            'title' => '📢 Important Announcement from Administration',
            'body' => '',
            'action_url' => route('student-portal'),
        ]);
    }
}
