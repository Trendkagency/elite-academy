<?php

namespace App\Filament\Resources\TeacherProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
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
                    ->getStateUsing(fn($record) => $record->photo_url)
                    ->defaultImageUrl(fn($record) => $record?->photo_url ?? 'https://ui-avatars.com/api/?name=Teacher&background=4F46E5&color=fff'),
                TextColumn::make('user.name')
                    ->label(__('Teacher Name'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->wrap()
                    ->lineClamp(2),
                TextColumn::make('specialization')
                    ->label(__('Specialization'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('years_experience')
                    ->label(__('Exp. (Years)'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rating_avg')
                    ->label(__('Rating'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('students_count')
                    ->label(__('Students'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.status')
                    ->label(__('Account Status'))
                    ->badge(),
                ToggleColumn::make('is_public')
                    ->label(__('Publicly Visible')),
                ToggleColumn::make('is_featured')
                    ->label(__('Featured')),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('approveAccount')
                    ->label(__('Approve Teacher'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->user?->update(['status' => \App\Enums\AccountStatus::APPROVED]);
                        \Filament\Notifications\Notification::make()->title(__('Teacher Approved'))->success()->send();
                    })
                    ->visible(fn($record) => $record->user?->status !== \App\Enums\AccountStatus::APPROVED && $record->user?->status !== 'approved'),
                \Filament\Actions\Action::make('rejectAccount')
                    ->label(__('Reject Teacher'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->user?->update(['status' => \App\Enums\AccountStatus::REJECTED]);
                        \Filament\Notifications\Notification::make()->title(__('Teacher Rejected'))->warning()->send();
                    })
                    ->visible(fn($record) => $record->user?->status === \App\Enums\AccountStatus::PENDING || $record->user?->status === 'pending'),
                ViewAction::make(),
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
            ])
            ->defaultSort('created_at', 'desc');
    }
}
