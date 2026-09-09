<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    protected static ?string $title = '📚 Linked Subjects (المواد الدراسية التابعة لهذا القسم)';

    protected static string|BackedEnum|null $icon = 'heroicon-o-book-open';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Subject Name (اسم المادة)')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?: 'subject'))),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(100),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Active Status')
                    ->default(true),

                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('Subject Cover Image')
                    ->disk('public')
                    ->directory('subjects')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Subject Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('courses_count')
                    ->counts('courses')
                    ->label('Courses (الكورسات)')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->headerActions([
                CreateAction::make()
                    ->label('➕ Add New Subject under Category'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
