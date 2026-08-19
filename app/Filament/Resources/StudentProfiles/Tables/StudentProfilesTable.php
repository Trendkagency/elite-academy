<?php

namespace App\Filament\Resources\StudentProfiles\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StudentProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Student Email')
                    ->searchable(),
                TextColumn::make('user.phone')
                    ->label('Phone Number')
                    ->searchable(),
                TextColumn::make('gradeLevel.name')
                    ->label('Grade Level')
                    ->badge()
                    ->sortable(),
                TextColumn::make('school_name')
                    ->label('School Name')
                    ->searchable(),
                TextColumn::make('user.status')
                    ->label('Account Status')
                    ->badge(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approveStudent')
                    ->label('Approve Student')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->user?->update(['status' => \App\Enums\AccountStatus::APPROVED]);
                        Notification::make()->title('Student Approved')->success()->send();
                    })
                    ->visible(fn ($record) => $record->user?->status !== \App\Enums\AccountStatus::APPROVED && $record->user?->status !== 'approved'),
                Action::make('rejectStudent')
                    ->label('Reject Student')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->user?->update(['status' => \App\Enums\AccountStatus::REJECTED]);
                        Notification::make()->title('Student Rejected')->warning()->send();
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
            ]);
    }
}
