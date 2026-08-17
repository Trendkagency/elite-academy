<?php

namespace App\Filament\Resources\ExceptionRequests\Pages;

use App\Filament\Resources\ExceptionRequests\ExceptionRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExceptionRequests extends ListRecords
{
    protected static string $resource = ExceptionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
