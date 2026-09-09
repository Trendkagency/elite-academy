<?php

namespace App\Filament\Resources\Subjects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Subject'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                ImageColumn::make('image')
                    ->label(__('Image')),
                TextColumn::make('active_courses_count')
                    ->label(__('Courses'))
                    ->state(fn ($record) => $record->getActiveCoursesCount())
                    ->badge()
                    ->color('warning'),
                TextColumn::make('video_lessons_count')
                    ->label(__('Lessons'))
                    ->state(fn ($record) => $record->getVideoLessonsCount())
                    ->badge()
                    ->color('info'),
                TextColumn::make('students_count')
                    ->label(__('Students'))
                    ->state(fn ($record) => number_format($record->getActiveStudentsCount()))
                    ->badge()
                    ->color('success'),
                TextColumn::make('rating_avg')
                    ->label(__('Rating'))
                    ->state(fn ($record) => number_format($record->getRatingAvg(), 1) . ' ★')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('Deleted At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('Filter by Category'))
                    ->relationship('category', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
