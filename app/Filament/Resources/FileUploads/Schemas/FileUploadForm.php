<?php

namespace App\Filament\Resources\FileUploads\Schemas;

use Filament\Forms\Components\DateTimePicker;
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

                        Select::make('category')
                            ->label(__('File Purpose / Category (نوع الملف / الغرض)'))
                            ->options([
                                'material' => __('Study Material / مذكرة دراسية'),
                                'homework' => __('Homework Assignment / واجب منزلي وتكليف'),
                                'submission' => __('Student Submission / تسليم وحل طالب'),
                            ])
                            ->default('material')
                            ->required()
                            ->live()
                            ->columnSpan(1),

                        DateTimePicker::make('due_at')
                            ->label(__('Submission Deadline / آخر موعد للتسليم'))
                            ->required(fn ($get) => $get('category') === 'homework')
                            ->visible(fn ($get) => $get('category') === 'homework')
                            ->helperText(__('When saved as homework, students will see this deadline in their assignments hub.'))
                            ->columnSpan(1),

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
                            ->relationship(
                                name: 'teacherProfile',
                                titleAttribute: 'id',
                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->with('user')->latest('created_at')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($record->user?->name ?: 'Teacher #' . $record->id) . ($record->specialization ? ' — ' . $record->specialization : ''))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('student_user_id')
                            ->label(__('Specific Student (Optional)'))
                            ->relationship(
                                name: 'studentUser',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereHas('studentProfile')->latest('created_at')
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('assignment_id')
                            ->label(__('Linked Assignment / الواجب المرتبط (Optional)'))
                            ->relationship('assignment', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn ($get) => $get('category') === 'homework'),
                    ]),
            ]);
    }
}
