<?php

namespace App\Filament\Resources\AssignmentSubmissions;

use App\Filament\Resources\AssignmentSubmissions\Pages\CreateAssignmentSubmission;
use App\Filament\Resources\AssignmentSubmissions\Pages\EditAssignmentSubmission;
use App\Filament\Resources\AssignmentSubmissions\Pages\ListAssignmentSubmissions;
use App\Filament\Resources\AssignmentSubmissions\Schemas\AssignmentSubmissionForm;
use App\Filament\Resources\AssignmentSubmissions\Tables\AssignmentSubmissionsTable;
use App\Models\AssignmentSubmission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssignmentSubmissionResource extends Resource
{
    protected static ?string $model = AssignmentSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الكورسات والواجبات' : 'Course & Exam Management';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تسليمات الطلاب والدرجات' : 'Submissions & Grades';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تسليم إجابة' : 'Submission';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تسليمات الطلاب' : 'Submissions';
    }

    public static function form(Schema $schema): Schema
    {
        return AssignmentSubmissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssignmentSubmissionsTable::configure($table);
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
            'index' => ListAssignmentSubmissions::route('/'),
            'create' => CreateAssignmentSubmission::route('/create'),
            'edit' => EditAssignmentSubmission::route('/{record}/edit'),
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
