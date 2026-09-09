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
                    ->label('Course Title (اسم المقرر)')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.category.name')
                    ->label('Category (القسم)')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Subject (المادة)')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gradeLevel.name')
                    ->label('Grade Level (الصف)')
                    ->searchable(),
                TextColumn::make('teacher.title')
                    ->label('Teacher')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image')
                    ->label('Image'),
                TextColumn::make('sessions_count')
                    ->label('Sessions')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('session_duration_minutes')
                    ->label('Duration (m)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rating_avg')
                    ->label('Rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reviews_count')
                    ->label('Reviews')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('enrollments_count')
                    ->label('Students')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('has_free_demo')
                    ->label('Free Demo')
                    ->boolean(),
                IconColumn::make('is_accredited')
                    ->label('Accredited')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Filter by Category (حسب القسم)')
                    ->relationship('subject.category', 'name'),
                SelectFilter::make('subject_id')
                    ->label('Filter by Subject (حسب المادة)')
                    ->relationship('subject', 'name'),
                SelectFilter::make('grade_level_id')
                    ->label('Filter by Grade Level (حسب الصف)')
                    ->relationship('gradeLevel', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                \Filament\Actions\RestoreAction::make(),
                \Filament\Actions\ForceDeleteAction::make(),
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
