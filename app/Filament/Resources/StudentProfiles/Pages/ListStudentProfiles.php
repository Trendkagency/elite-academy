<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use App\Models\StudentProfile;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;

class ListStudentProfiles extends ListRecords
{
    protected static string $resource = StudentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Students (جميع الطلاب)')
                ->badge(StudentProfile::count()),
            'pending' => Tab::make('⏳ Pending Approval (قيد المراجعة)')
                ->badge(StudentProfile::whereHas('user', fn ($q) => $q->where('status', \App\Enums\AccountStatus::PENDING))->count())
                ->modifyQueryUsing(fn ($query) => $query->whereHas('user', fn ($q) => $q->where('status', \App\Enums\AccountStatus::PENDING))),
            'approved' => Tab::make('✅ Approved (مقبول)')
                ->badge(StudentProfile::whereHas('user', fn ($q) => $q->where('status', \App\Enums\AccountStatus::APPROVED))->count())
                ->modifyQueryUsing(fn ($query) => $query->whereHas('user', fn ($q) => $q->where('status', \App\Enums\AccountStatus::APPROVED))),
        ];
    }
}
