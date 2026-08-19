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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentPackageResource extends Resource
{
    protected static ?string $model = StudentPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الباقات والمالية' : 'Packages & Finance';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? '🎟️ باقات اشتراكات الطلاب' : '🎟️ Student Packages';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'باقة طالب' : 'Student Package';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'باقات اشتراكات الطلاب' : 'Student Packages & Credits';
    }

    protected static ?string $recordTitleAttribute = 'student_user_id';

    public static function getRecordTitle($record): ?string
    {
        return $record->studentUser?->name . ' — ' . ($record->packageTemplate?->name ?? 'Custom Package');
    }

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListStudentPackages::route('/'),
            'create' => CreateStudentPackage::route('/create'),
            'edit'   => EditStudentPackage::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::where('status', 'active')->count();
        return $active > 0 ? (string) $active : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
