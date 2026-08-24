<?php

namespace App\Filament\Resources\MeetingProviders;

use App\Models\MeetingProvider;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MeetingProviderResource extends Resource
{
    protected static ?string $model = MeetingProvider::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الشؤون الأكاديمية' : 'Academic Management';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مزودو الاجتماعات المباشرة' : 'Meeting Providers';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('slug')
                ->required()
                ->maxLength(100),
            Toggle::make('is_active')
                ->default(true),
            Toggle::make('supports_embedding')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('slug')->badge(),
                IconColumn::make('is_active')->boolean(),
                IconColumn::make('supports_embedding')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeetingProviders::route('/'),
            'create' => Pages\CreateMeetingProvider::route('/create'),
            'edit' => Pages\EditMeetingProvider::route('/{record}/edit'),
        ];
    }
}
