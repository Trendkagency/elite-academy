<?php

namespace App\Filament\Resources\CourseSessions;

use App\Filament\Resources\CourseSessions\Pages\CreateCourseSession;
use App\Filament\Resources\CourseSessions\Pages\EditCourseSession;
use App\Filament\Resources\CourseSessions\Pages\ListCourseSessions;
use App\Filament\Resources\CourseSessions\Schemas\CourseSessionForm;
use App\Filament\Resources\CourseSessions\Tables\CourseSessionsTable;
use App\Models\CourseSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseSessionResource extends Resource
{
    protected static ?string $model = CourseSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الشؤون الأكاديمية' : 'Academic Management';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الحصص والبث المباشر' : 'Live Sessions & Classes';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'حصة دراسية' : 'Live Session';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الحصص المباشرة' : 'Live Sessions';
    }

    public static function form(Schema $schema): Schema
    {
        return CourseSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseSessionsTable::configure($table);
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
            'index' => ListCourseSessions::route('/'),
            'create' => CreateCourseSession::route('/create'),
            'edit' => EditCourseSession::route('/{record}/edit'),
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
