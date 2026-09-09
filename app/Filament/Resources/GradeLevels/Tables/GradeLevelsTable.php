<?php

namespace App\Filament\Resources\GradeLevels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradeLevelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Grade Level Name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('courses_count')
                    ->counts('courses')
                    ->label(__('Linked Courses'))
                    ->badge()
                    ->color('info'),

                TextColumn::make('student_profiles_count')
                    ->counts('studentProfiles')
                    ->label(__('Students'))
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}