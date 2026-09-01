<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Enums\AccountStatus;
use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use App\Models\PackageTemplate;
use App\Models\StudentPackage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStudentProfile extends EditRecord
{
    protected static string $resource = StudentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approveStudent')
                ->label('Approve Student')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->user?->update(['status' => AccountStatus::APPROVED]);
                    Notification::make()->title('Student Account Approved')->success()->send();
                })
                ->visible(fn () => $this->record->user?->status !== AccountStatus::APPROVED && $this->record->user?->status !== 'approved'),

            Action::make('rejectStudent')
                ->label('Reject Student')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->user?->update(['status' => AccountStatus::REJECTED]);
                    Notification::make()->title('Student Account Rejected')->warning()->send();
                })
                ->visible(fn () => $this->record->user?->status === AccountStatus::PENDING || $this->record->user?->status === 'pending'),

            Action::make('assignPackage')
                ->label('🎟️ Assign Package / Credits')
                ->icon('heroicon-o-credit-card')
                ->color('primary')
                ->form([
                    Select::make('package_template_id')
                        ->label('Select Package Plan Template')
                        ->options(fn () => PackageTemplate::where('is_active', true)->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->helperText('Select a pre-defined template or leave empty for custom package.'),
                    TextInput::make('total_sessions')
                        ->label('Total Session Credits')
                        ->numeric()
                        ->default(12)
                        ->minValue(1)
                        ->required()
                        ->suffix('sessions'),
                ])
                ->action(function (array $data) {
                    StudentPackage::create([
                        'student_user_id' => $this->record->user_id,
                        'package_template_id' => $data['package_template_id'] ?? null,
                        'total_sessions' => (int) $data['total_sessions'],
                        'remaining_sessions' => (int) $data['total_sessions'],
                        'used_sessions' => 0,
                        'status' => 'active',
                        'activated_at' => now(),
                    ]);
                    Notification::make()
                        ->title("✅ Assigned {$data['total_sessions']} Session Credits to Student")
                        ->success()
                        ->send();
                }),

            DeleteAction::make()->label('Recycle Bin'),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $status = $this->data['user_status'] ?? $this->form->getRawState()['user_status'] ?? null;
        if ($status && $this->record->user) {
            $this->record->user->update(['status' => $status]);
        }
    }
}
