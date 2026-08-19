<?php

namespace App\Filament\Resources\ExceptionRequests\Pages;

use App\Filament\Resources\ExceptionRequests\ExceptionRequestResource;
use App\Services\Notification\FcmNotificationService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExceptionRequest extends EditRecord
{
    protected static string $resource = ExceptionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        if (in_array($record->status, ['approved', 'rejected'], true)) {
            app(FcmNotificationService::class)->notifyExceptionStatus($record);
        }
    }
}
