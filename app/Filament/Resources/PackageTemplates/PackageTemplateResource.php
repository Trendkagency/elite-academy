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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageTemplateResource extends Resource
{
    protected static ?string $model = PackageTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الباقات والمالية' : 'Packages & Finance';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'نماذج الباقات والخطط' : 'Package Plan Templates';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'خطة باقة' : 'Package Template';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'نماذج الباقات' : 'Package Templates';
    }

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

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
