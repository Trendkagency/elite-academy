<?php

namespace App\Filament\Resources\ParentProfiles;

use App\Filament\Resources\ParentProfiles\Pages\CreateParentProfile;
use App\Filament\Resources\ParentProfiles\Pages\EditParentProfile;
use App\Filament\Resources\ParentProfiles\Pages\ListParentProfiles;
use App\Filament\Resources\ParentProfiles\Schemas\ParentProfileForm;
use App\Filament\Resources\ParentProfiles\Tables\ParentProfilesTable;
use App\Models\ParentProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ParentProfileResource extends Resource
{
    protected static ?string $model = ParentProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ParentProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParentProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParentProfiles::route('/'),
            'create' => CreateParentProfile::route('/create'),
            'edit' => EditParentProfile::route('/{record}/edit'),
        ];
    }
}
