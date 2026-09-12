<?php

namespace App\Filament\Resources\RecurringSchedules\Pages;

use App\Filament\Resources\RecurringSchedules\RecurringScheduleResource;
use App\Services\Session\RecurringScheduleService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRecurringSchedule extends CreateRecord
{
    protected static string $resource = RecurringScheduleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $service = app(RecurringScheduleService::class);
        return $service->createSchedule($data, auth()->user());
    }
}
