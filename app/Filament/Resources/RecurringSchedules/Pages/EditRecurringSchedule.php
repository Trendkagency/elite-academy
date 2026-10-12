<?php

namespace App\Filament\Resources\RecurringSchedules\Pages;

use App\Filament\Resources\RecurringSchedules\RecurringScheduleResource;
use App\Services\Session\RecurringScheduleService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRecurringSchedule extends EditRecord
{
    protected static string $resource = RecurringScheduleResource::class;

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'تعديل الجدول الأسبوعي المتكرر' : 'Edit Recurring Schedule';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(RecurringScheduleService::class)->updateEntireSchedule($record, $data, auth()->user());
        return $record->fresh();
    }
}
