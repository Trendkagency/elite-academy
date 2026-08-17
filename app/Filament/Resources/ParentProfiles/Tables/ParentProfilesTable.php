<?php

namespace App\Filament\Resources\ParentProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParentProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Parent Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Parent Email')
                    ->searchable(),
                TextColumn::make('user.phone')
                    ->label('Phone Number')
                    ->searchable(),
                TextColumn::make('students.name')
                    ->label('Linked Children')
                    ->badge()
                    ->separator(', '),
                TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Children Count')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
