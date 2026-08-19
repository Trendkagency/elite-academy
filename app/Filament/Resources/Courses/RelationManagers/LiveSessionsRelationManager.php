<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class LiveSessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'liveSessions';

    protected static ?string $title = '📡 Scheduled Live Attendance Streams (البث المباشر والمواعيد)';

    protected static string|BackedEnum|null $icon = 'heroicon-o-video-camera';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_user_id')
                    ->relationship('studentUser', 'name')
                    ->label('Student (Leave empty for global live stream)')
                    ->searchable()
                    ->nullable(),
                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled Date & Time')
                    ->required(),
                TextInput::make('duration_minutes')
                    ->label('Duration (Minutes)')
                    ->numeric()
                    ->default(60)
                    ->required(),
                TextInput::make('meeting_link')
                    ->label('Google Meet / Zoom Meeting Link')
                    ->url()
                    ->placeholder('https://meet.google.com/...'),
                Select::make('meeting_platform')
                    ->options([
                        'google_meet' => 'Google Meet',
                        'zoom' => 'Zoom Meeting',
                        'microsoft_teams' => 'Microsoft Teams',
                    ])
                    ->default('google_meet'),
                Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled (مجدولة)',
                        'link_visible' => 'Link Visible (الرابط مفعل)',
                        'in_progress' => 'In Progress (جارية الآن)',
                        'completed' => 'Completed (مكتملة)',
                        'cancelled' => 'Cancelled (ملغاة)',
                    ])
                    ->default('scheduled'),
                Toggle::make('is_free_demo')
                    ->label('🎓 Free Trial Demo Session (حصة مجانية)')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Scheduled Time')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('studentUser.name')
                    ->label('Student')
                    ->default('All Enrolled Students'),
                TextColumn::make('meeting_platform')
                    ->label('Platform'),
                TextColumn::make('meeting_link')
                    ->label('Meeting Link')
                    ->limit(25),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'in_progress', 'link_visible' => 'info',
                        'scheduled' => 'primary',
                        default => 'danger',
                    }),
                ToggleColumn::make('is_free_demo')
                    ->label('Free Demo'),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()->label('📡 Schedule Live Session'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }
}
