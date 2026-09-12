<?php

namespace App\Filament\Resources\RecurringSchedules\Pages;

use App\Filament\Resources\RecurringSchedules\RecurringScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecurringSchedule extends EditRecord
{
    protected static string $resource = RecurringScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
