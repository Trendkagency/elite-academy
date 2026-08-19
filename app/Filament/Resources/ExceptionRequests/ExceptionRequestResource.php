<?php

namespace App\Filament\Resources\ExceptionRequests;

use App\Filament\Resources\ExceptionRequests\Pages\CreateExceptionRequest;
use App\Filament\Resources\ExceptionRequests\Pages\EditExceptionRequest;
use App\Filament\Resources\ExceptionRequests\Pages\ListExceptionRequests;
use App\Filament\Resources\ExceptionRequests\Schemas\ExceptionRequestForm;
use App\Filament\Resources\ExceptionRequests\Tables\ExceptionRequestsTable;
use App\Models\ExceptionRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExceptionRequestResource extends Resource
{
    protected static ?string $model = ExceptionRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'التواصل والدعم الفني' : 'Communication & Support';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'طلبات الاستثناءات والأعذار' : 'Exception Requests';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'طلب استثناء' : 'Exception Request';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'طلبات الاستثناءات' : 'Exception Requests';
    }

    public static function form(Schema $schema): Schema
    {
        return ExceptionRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExceptionRequestsTable::configure($table);
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
            'index' => ListExceptionRequests::route('/'),
            'create' => CreateExceptionRequest::route('/create'),
            'edit' => EditExceptionRequest::route('/{record}/edit'),
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
