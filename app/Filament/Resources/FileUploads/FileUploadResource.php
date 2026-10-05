<?php

namespace App\Filament\Resources\FileUploads;

use App\Filament\Resources\FileUploads\Pages\CreateFileUpload;
use App\Filament\Resources\FileUploads\Pages\EditFileUpload;
use App\Filament\Resources\FileUploads\Pages\ListFileUploads;
use App\Filament\Resources\FileUploads\Schemas\FileUploadForm;
use App\Filament\Resources\FileUploads\Tables\FileUploadsTable;
use App\Models\FileUpload;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FileUploadResource extends Resource
{
    protected static ?string $model = FileUpload::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowUp;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة الملفات والمرفقات' : 'Files & Documents';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الملفات والمذكرات التعليمية' : 'Educational Files';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'ملف تعليمي' : 'File';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الملفات التعليمية' : 'Files';
    }

    public static function form(Schema $schema): Schema
    {
        return FileUploadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FileUploadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFileUploads::route('/'),
            'create' => CreateFileUpload::route('/create'),
            'edit' => EditFileUpload::route('/{record}/edit'),
        ];
    }
}
