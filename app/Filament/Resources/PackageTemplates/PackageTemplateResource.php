<?php

namespace App\Filament\Resources\PackageTemplates;

use App\Filament\Resources\PackageTemplates\Pages\CreatePackageTemplate;
use App\Filament\Resources\PackageTemplates\Pages\EditPackageTemplate;
use App\Filament\Resources\PackageTemplates\Pages\ListPackageTemplates;
use App\Filament\Resources\PackageTemplates\Schemas\PackageTemplateForm;
use App\Filament\Resources\PackageTemplates\Tables\PackageTemplatesTable;
use App\Models\PackageTemplate;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackageTemplateResource extends Resource
{
    protected static ?string $model = PackageTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = 'Packages & Wallet';

    protected static ?string $navigationLabel = 'Package Plan Templates';

    public static function form(Schema $schema): Schema
    {
        return PackageTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackageTemplatesTable::configure($table);
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
            'index' => ListPackageTemplates::route('/'),
            'create' => CreatePackageTemplate::route('/create'),
            'edit' => EditPackageTemplate::route('/{record}/edit'),
        ];
    }
}
