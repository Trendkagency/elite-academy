<?php

namespace App\Filament\Resources\ExceptionRequests\Pages;

use App\Filament\Resources\ExceptionRequests\ExceptionRequestResource;
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
}
