<?php

namespace App\Filament\Resources\AssignmentSubmissions\Tables;

use App\Enums\SubmissionStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssignmentSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('assignment.title')
                    ->label('Assignment Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('studentUser.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grade')
                    ->label('Score / Grade (%)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match (is_object($state) ? $state->value : $state) {
                        'completed' => 'success',
                        'late' => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('gradeSubmission')
                    ->label('Grade & Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('grade')
                            ->label('Grade Percentage (%)')
                            ->numeric()
                            ->default(100)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'grade' => $data['grade'],
                            'status' => SubmissionStatus::COMPLETED->value,
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]);
                        Notification::make()
                            ->title('Homework Submission Graded & Approved')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
