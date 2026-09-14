<?php

namespace App\Filament\Resources\ParentProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ParentProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('Parent Name'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label(__('Parent Email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.phone')
                    ->label(__('Phone Number'))
                    ->searchable(),
                TextColumn::make('students.name')
                    ->label(__('Linked Children'))
                    ->badge()
                    ->separator(', ')
                    ->wrap(),
                TextColumn::make('user.status')
                    ->label(__('Account Status'))
                    ->badge(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('approveAccount')
                    ->label(__('Approve Parent'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->user?->update(['status' => \App\Enums\AccountStatus::APPROVED]);
                        \Filament\Notifications\Notification::make()->title(__('Parent Approved'))->success()->send();
                    })
                    ->visible(fn ($record) => $record->user?->status !== \App\Enums\AccountStatus::APPROVED && $record->user?->status !== 'approved'),
                \Filament\Actions\Action::make('rejectAccount')
                    ->label(__('Reject Parent'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->user?->update(['status' => \App\Enums\AccountStatus::REJECTED]);
                        \Filament\Notifications\Notification::make()->title('Parent Rejected')->warning()->send();
                    })
                    ->visible(fn ($record) => $record->user?->status === \App\Enums\AccountStatus::PENDING || $record->user?->status === 'pending'),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
