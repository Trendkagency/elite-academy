<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Account Role')
                    ->state(fn ($record) => match (true) {
                        $record->isAdmin() => 'Admin ⚡',
                        $record->isTeacher() => 'Teacher 👨‍🏫',
                        $record->isParent() => 'Parent 👨‍👩‍👧',
                        default => 'Student 🎓',
                    })
                    ->badge()
                    ->color(fn ($state) => str_contains($state, 'Admin') ? 'danger' : (str_contains($state, 'Teacher') ? 'info' : (str_contains($state, 'Parent') ? 'primary' : 'success'))),
                TextColumn::make('status')
                    ->label('Account Status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Approval Status')
                    ->options([
                        'pending' => '⏳ Pending Approval (قيد المراجعة)',
                        'approved' => '✅ Approved (مقبول)',
                        'rejected' => '❌ Rejected (مرفوض)',
                        'suspended' => '🚫 Suspended (معلق)',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approveAccount')
                    ->label('Approve Account')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['status' => \App\Enums\AccountStatus::APPROVED]);
                        Notification::make()
                            ->title('Account Approved')
                            ->body("Account for {$record->name} has been approved successfully.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status !== \App\Enums\AccountStatus::APPROVED && $record->status !== 'approved'),
                Action::make('rejectAccount')
                    ->label('Reject Account')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => \App\Enums\AccountStatus::REJECTED]);
                        Notification::make()
                            ->title('Account Rejected')
                            ->body("Account for {$record->name} has been rejected.")
                            ->warning()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === \App\Enums\AccountStatus::PENDING || $record->status === 'pending'),
                EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
                \Filament\Actions\RestoreAction::make(),
                \Filament\Actions\ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label('Approve Selected Accounts')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => \App\Enums\AccountStatus::APPROVED]);
                            }
                            Notification::make()
                                ->title('Accounts Approved')
                                ->body(count($records) . ' accounts approved successfully.')
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('rejectSelected')
                        ->label('Reject Selected Accounts')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => \App\Enums\AccountStatus::REJECTED]);
                            }
                            Notification::make()
                                ->title('Accounts Rejected')
                                ->body(count($records) . ' accounts rejected.')
                                ->warning()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
