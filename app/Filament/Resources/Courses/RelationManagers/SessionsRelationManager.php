<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';

    protected static ?string $title = '📚 Course Curriculum Sessions & Free Demo Policy (حصص الكورس وتخصيص الجلسة المجانية)';

    protected static string|BackedEnum|null $icon = 'heroicon-o-book-open';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Session Title (عنوان الحصّة)')
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled Date & Time (تاريخ وتوقيت الحصة المحددة)')
                    ->nullable(),
                DateTimePicker::make('start_at')
                    ->label('Start Date & Time (وقت البدء)')
                    ->nullable(),
                DateTimePicker::make('end_at')
                    ->label('End Date & Time (وقت الانتهاء)')
                    ->nullable(),
                TextInput::make('sort_order')
                    ->label('Sort Order (الترتيب)')
                    ->numeric()
                    ->default(1)
                    ->required(),
                TextInput::make('duration_minutes')
                    ->label('Duration (Minutes)')
                    ->numeric()
                    ->default(60)
                    ->required(),
                TextInput::make('video_url')
                    ->label('Video URL / Stream Link')
                    ->url()
                    ->placeholder('https://www.youtube.com/watch?v=...'),
                Toggle::make('is_free_demo')
                    ->label('🎓 Free Demo Session (حصّة تجريبية مجانية)')
                    ->helperText('When enabled, any student can attend this session for free without package credits or assignment gating.')
                    ->default(false),
                Textarea::make('description')
                    ->label('Session Summary')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Session Title')
                    ->searchable(),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' mins'),
                IconColumn::make('is_free_demo')
                    ->label('Demo Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-academic-cap')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('gray'),
                ToggleColumn::make('is_free_demo')
                    ->label('Toggle Free Demo'),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()->label('➕ Add New Course Session'),
            ])
            ->recordActions([
                Action::make('mark_as_free')
                    ->label('Make Free Demo 🎓')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['is_free_demo' => true]);
                        Notification::make()
                            ->title('Session Marked as Free Trial Demo 🎓')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => ! $record->is_free_demo),

                Action::make('mark_as_regular')
                    ->label('Set as Regular 🔒')
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->action(function ($record) {
                        $record->update(['is_free_demo' => false]);
                        Notification::make()
                            ->title('Session Set to Regular Package Credit Session 🔒')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn ($record) => (bool) $record->is_free_demo),

                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }
}
