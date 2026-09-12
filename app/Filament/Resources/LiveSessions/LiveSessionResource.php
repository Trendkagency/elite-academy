<?php

namespace App\Filament\Resources\LiveSessions;

use App\Filament\Resources\LiveSessions\Pages\CreateLiveSession;
use App\Filament\Resources\LiveSessions\Pages\EditLiveSession;
use App\Filament\Resources\LiveSessions\Pages\ListLiveSessions;
use App\Filament\Resources\LiveSessions\Schemas\LiveSessionForm;
use App\Filament\Resources\LiveSessions\Tables\LiveSessionsTable;
use App\Models\LiveSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LiveSessionResource extends Resource
{
    protected static ?string $model = LiveSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الشؤون الأكاديمية' : 'Academic Management';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'حصص البث المباشر والروابط' : 'Live Teaching & Meeting Links';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'حصة بث مباشر' : 'Live Session';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'حصص البث المباشر والروابط' : 'Live Teaching & Meeting Links';
    }

    public static function form(Schema $schema): Schema
    {
        return LiveSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LiveSessionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLiveSessions::route('/'),
            'create' => CreateLiveSession::route('/create'),
            'edit' => EditLiveSession::route('/{record}/edit'),
        ];
    }
}
