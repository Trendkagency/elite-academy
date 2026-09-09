<?php

namespace App\Filament\Resources\Courses\Tables;

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

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Course Title'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.category.name')
                    ->label(__('Category'))
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label(__('Subject'))
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gradeLevel.name')
                    ->label(__('Grade Level'))
                    ->searchable(),
                TextColumn::make('teacher.title')
                    ->label(__('Teacher'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image')
                    ->label(__('Image')),
                TextColumn::make('sessions_count')
                    ->label(__('Sessions'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('session_duration_minutes')
                    ->label(__('Duration (min)'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rating_avg')
                    ->label(__('Rating'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reviews_count')
                    ->label(__('Reviews'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('enrollments_count')
                    ->label(__('Students'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('has_free_demo')
                    ->label(__('Free Demo'))
                    ->boolean(),
                IconColumn::make('is_accredited')
                    ->label(__('Accredited'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
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
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(__('Filter by Category'))
                    ->relationship('subject.category', 'name'),
                SelectFilter::make('subject_id')
                    ->label(__('Filter by Subject'))
                    ->relationship('subject', 'name'),
                SelectFilter::make('grade_level_id')
                    ->label(__('Filter by Grade Level'))
                    ->relationship('gradeLevel', 'name'),
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
