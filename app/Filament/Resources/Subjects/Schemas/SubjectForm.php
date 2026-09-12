<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label(__('Academic Category'))
                    ->relationship('category', 'name', fn ($query) => $query->orderBy('sort_order', 'asc'))
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label(__('Category Name'))
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?: 'category'))),
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->maxLength(100),
                        ColorPicker::make('color_theme')
                            ->label(__('Color Theme'))
                            ->default('#0D9488'),
                        TextInput::make('sort_order')
                            ->label(__('Sort Order'))
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label(__('Active Status'))
                            ->default(true),
                    ])
                    ->required(),
                TextInput::make('name')
                    ->label(__('Subject Name'))
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if (empty($get('slug')) || $get('slug') === Str::slug($state ?: 'subject')) {
                            $set('slug', Str::slug($state ?: 'subject'));
                        }
                    }),
                TextInput::make('slug')
                    ->label(__('Slug / Identifier'))
                    ->required()
                    ->maxLength(150)
                    ->unique(ignoreRecord: true)
                    ->helperText(__('Auto-generated from name. Must be unique across all subjects.')),
                Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label(__('Subject Cover Image (Drag & Drop)'))
                    ->disk('public')
                    ->directory('subjects')
                    ->visibility('public')
                    ->image()
                    ->imageEditor(),
                TextInput::make('rating_avg')
                    ->label(__('Student Rating Override'))
                    ->numeric()
                    ->step('0.1')
                    ->minValue(0)
                    ->maxValue(5)
                    ->placeholder('e.g. 4.9')
                    ->helperText(__('Leave blank to calculate dynamically from courses & teachers.')),
                TextInput::make('students_count')
                    ->label(__('Active Students Override'))
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('e.g. 2450')
                    ->helperText(__('Leave blank to calculate dynamically from enrollments & teachers.')),
                TextInput::make('video_lessons_count')
                    ->label(__('Video Lessons Override'))
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('e.g. 48')
                    ->helperText(__('Leave blank to calculate dynamically from course sessions.')),
                TextInput::make('active_courses_count')
                    ->label(__('Active Courses Override'))
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('e.g. 2')
                    ->helperText(__('Leave blank to calculate dynamically from active courses.')),
                TextInput::make('sort_order')
                    ->label(__('Sort Order'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label(__('Active Status'))
                    ->default(true)
                    ->required(),
            ]);
    }
}
