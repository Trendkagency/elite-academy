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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParentProfileResource extends Resource
{
    protected static ?string $model = ParentProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة المستخدمين والأدوار' : 'User Management';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'حسابات أولياء الأمور' : 'Parent Profiles';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'ملف ولي أمر' : 'Parent Profile';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'حسابات أولياء الأمور' : 'Parent Profiles';
    }

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

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
