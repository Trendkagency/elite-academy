<?php

namespace App\Filament\Resources\TeacherProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TeacherProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->circular()
                    ->label('Photo'),
                TextColumn::make('user.name')
                    ->label('Teacher Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),
                TextColumn::make('specialization')
                    ->label('Specialization')
                    ->badge()
                    ->searchable(),
                TextColumn::make('years_experience')
                    ->label('Exp. (Years)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rating_avg')
                    ->label('Rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('students_count')
                    ->label('Students')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_public')
                    ->label('Publicly Visible'),
                ToggleColumn::make('is_featured')
                    ->label('Featured'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
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
