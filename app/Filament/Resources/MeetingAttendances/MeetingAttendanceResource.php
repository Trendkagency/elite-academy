<?php

namespace App\Filament\Resources\MeetingAttendances;

use App\Models\MeetingAttendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MeetingAttendanceResource extends Resource
{
    protected static ?string $model = MeetingAttendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 11;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الشؤون الأكاديمية' : 'Academic Management';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'سجل حضور البث المباشر' : 'Live Meeting Attendance';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('studentUser.name')
                    ->label(app()->getLocale() === 'ar' ? 'الطالب' : 'Student')
                    ->searchable(),
                TextColumn::make('liveSession.title')
                    ->label(app()->getLocale() === 'ar' ? 'الحصة' : 'Session')
                    ->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('duration_seconds')
                    ->label(app()->getLocale() === 'ar' ? 'مدة الحضور' : 'Duration')
                    ->formatStateUsing(fn (int $state) => gmdate('H:i:s', $state))
                    ->sortable(),
                TextColumn::make('joined_at')->dateTime()->sortable(),
                TextColumn::make('last_seen_at')->dateTime()->sortable(),
                TextColumn::make('ip_address')->searchable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeetingAttendances::route('/'),
        ];
    }
}
