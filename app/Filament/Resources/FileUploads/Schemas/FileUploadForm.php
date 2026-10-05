<?php

namespace App\Filament\Resources\FileUploads\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FileUploadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('File Details'))
                    ->columns(2)
                    ->components([
                        TextInput::make('title')
                            ->label(__('File Title'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpan(2),

                        Textarea::make('description')
                            ->label(__('Description / Instructions'))
                            ->rows(3)
                            ->nullable()
                            ->columnSpan(2),

                        FileUpload::make('file_path')
                            ->label(__('Upload Document or Image'))
                            ->disk('public')
                            ->directory('educational_files')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/gif',
                            ])
                            ->maxSize(25600) // 25 MB
                            ->preserveFilenames()
                            ->storeFileNamesIn('original_name')
                            ->required()
                            ->columnSpan(2),
                    ]),

                Section::make(__('Academic Associations & Target'))
                    ->columns(3)
                    ->components([
                        Select::make('course_id')
                            ->label(__('Course'))
                            ->relationship('course', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('teacher_profile_id')
                            ->label(__('Teacher'))
                            ->relationship('teacherProfile', modifyQueryUsing: fn ($q) => $q->with('user'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?: 'Teacher #' . $record->id)
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('student_user_id')
                            ->label(__('Specific Student (Optional)'))
                            ->relationship('studentUser', 'name', modifyQueryUsing: fn ($q) => $q->where('role', 'student'))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),
            ]);
    }
}
