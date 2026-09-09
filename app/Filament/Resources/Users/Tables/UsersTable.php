<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
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
                ImageColumn::make('avatar')
                    ->label(__('Avatar'))
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->avatar_url)
                    ->defaultImageUrl(fn ($record) => $record?->avatar_url ?? 'https://ui-avatars.com/api/?name=User&background=6366F1&color=fff'),
                TextColumn::make('name')
                    ->label(__('Full Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('Email Address'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('Phone Number'))
                    ->searchable(),
                TextColumn::make('role')
                    ->label(__('Account Role'))
                    ->state(fn ($record) => match ($record->getRoleName()) {
                        \App\Enums\Role::ADMIN->value => __('Admin'),
                        \App\Enums\Role::TEACHER->value => __('Teacher'),
                        \App\Enums\Role::PARENT->value => __('Parent'),
                        default => __('Student'),
                    })
                    ->badge()
                    ->icon(fn ($record) => match ($record->getRoleName()) {
                        \App\Enums\Role::ADMIN->value => 'heroicon-o-shield-check',
                        \App\Enums\Role::TEACHER->value => 'heroicon-o-academic-cap',
                        \App\Enums\Role::PARENT->value => 'heroicon-o-user-group',
                        default => 'heroicon-o-user',
                    })
                    ->color(fn ($record) => match ($record->getRoleName()) {
                        \App\Enums\Role::ADMIN->value => 'danger',
                        \App\Enums\Role::TEACHER->value => 'info',
                        \App\Enums\Role::PARENT->value => 'primary',
                        default => 'success',
                    }),
                TextColumn::make('status')
                    ->label(__('Account Approval Status'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(__('Filter by Role'))
                    ->options([
                        'student' => __('Student'),
                        'teacher' => __('Teacher'),
                        'parent'  => __('Parent'),
                        'admin'   => __('Admin'),
                    ])
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        return match ($data['value']) {
                            'student' => $query->roleStudent(),
                            'teacher' => $query->roleTeacher(),
                            'parent'  => $query->roleParent(),
                            'admin'   => $query->roleAdmin(),
                            default   => $query,
                        };
                    }),
                SelectFilter::make('status')
                    ->label(__('Account Approval Status'))
                    ->options([
                        'pending'   => __('Pending Approval'),
                        'approved'  => __('Approved'),
                        'rejected'  => __('Rejected'),
                        'suspended' => __('Suspended'),
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approveAccount')
                    ->label(__('Approve Account'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['status' => \App\Enums\AccountStatus::APPROVED]);
                        Notification::make()
                            ->title(__('Account Approved'))
                            ->body(__('Account for :name has been approved successfully.', ['name' => $record->name]))
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status !== \App\Enums\AccountStatus::APPROVED && $record->status !== 'approved'),
                Action::make('rejectAccount')
                    ->label(__('Reject Account'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => \App\Enums\AccountStatus::REJECTED]);
                        Notification::make()
                            ->title(__('Account Rejected'))
                            ->body(__('Account for :name has been rejected.', ['name' => $record->name]))
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
                        ->label(__('Approve Selected Accounts'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => \App\Enums\AccountStatus::APPROVED]);
                            }
                            Notification::make()
                                ->title(__('Accounts Approved'))
                                ->body(count($records) . ' ' . __('accounts approved successfully.'))
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('rejectSelected')
                        ->label(__('Reject Selected Accounts'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => \App\Enums\AccountStatus::REJECTED]);
                            }
                            Notification::make()
                                ->title(__('Accounts Rejected'))
                                ->body(count($records) . ' ' . __('accounts rejected.'))
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
