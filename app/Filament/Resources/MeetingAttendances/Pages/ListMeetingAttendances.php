<?php

namespace App\Filament\Resources\MeetingAttendances\Pages;

use App\Filament\Resources\MeetingAttendances\MeetingAttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListMeetingAttendances extends ListRecords
{
    protected static string $resource = MeetingAttendanceResource::class;
}
