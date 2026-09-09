<?php

namespace App\Filament\Resources\TeacherProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
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
                ImageColumn::make('photo')
                    ->label(__('Photo'))
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->photo_url)
                    ->defaultImageUrl(fn ($record) => $record?->photo_url ?? 'https://ui-avatars.com/api/?name=Teacher&background=4F46E5&color=fff'),
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
                TextColumn::make('user.status')
                    ->label('Account Status')
                    ->badge(),
                ToggleColumn::make('is_public')
                    ->label('Publicly Visible'),
                ToggleColumn::make('is_featured')
                    ->label('Featured'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('approveAccount')
                    ->label('Approve Teacher')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->user?->update(['status' => \App\Enums\AccountStatus::APPROVED]);
                        \Filament\Notifications\Notification::make()->title('Teacher Approved')->success()->send();
                    })
                    ->visible(fn ($record) => $record->user?->status !== \App\Enums\AccountStatus::APPROVED && $record->user?->status !== 'approved'),
                \Filament\Actions\Action::make('rejectAccount')
                    ->label('Reject Teacher')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->user?->update(['status' => \App\Enums\AccountStatus::REJECTED]);
                        \Filament\Notifications\Notification::make()->title('Teacher Rejected')->warning()->send();
                    })
                    ->visible(fn ($record) => $record->user?->status === \App\Enums\AccountStatus::PENDING || $record->user?->status === 'pending'),
                EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
