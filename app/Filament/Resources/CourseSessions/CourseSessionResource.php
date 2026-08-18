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

class CourseSessionResource extends Resource
{
    protected static ?string $model = CourseSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    protected static \UnitEnum|string|null $navigationGroup = 'Academic Management';

    protected static ?int $navigationSort = 3;

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
}
