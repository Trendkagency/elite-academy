<?php

namespace App\Filament\Resources\StudentPackages;

use App\Filament\Resources\StudentPackages\Pages\CreateStudentPackage;
use App\Filament\Resources\StudentPackages\Pages\EditStudentPackage;
use App\Filament\Resources\StudentPackages\Pages\ListStudentPackages;
use App\Filament\Resources\StudentPackages\Schemas\StudentPackageForm;
use App\Filament\Resources\StudentPackages\Tables\StudentPackagesTable;
use App\Models\StudentPackage;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudentPackageResource extends Resource
{
    protected static ?string $model = StudentPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static UnitEnum|string|null $navigationGroup = 'Packages & Wallet';

    protected static ?string $navigationLabel = 'Student Packages & Credits';

    public static function form(Schema $schema): Schema
    {
        return StudentPackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentPackagesTable::configure($table);
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
            'index' => ListStudentPackages::route('/'),
            'create' => CreateStudentPackage::route('/create'),
            'edit' => EditStudentPackage::route('/{record}/edit'),
        ];
    }
}
