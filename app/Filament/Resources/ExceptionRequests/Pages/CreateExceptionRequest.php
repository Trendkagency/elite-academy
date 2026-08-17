<?php

namespace App\Filament\Resources\ExceptionRequests\Pages;

use App\Filament\Resources\ExceptionRequests\ExceptionRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExceptionRequest extends CreateRecord
{
    protected static string $resource = ExceptionRequestResource::class;
}
