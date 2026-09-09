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
            'all' => Tab::make(__('All Students'))
                ->icon('heroicon-o-user-group')
                ->badge(StudentProfile::count()),
            'pending' => Tab::make(__('Pending Approval'))
                ->icon('heroicon-o-clock')
                ->badge(StudentProfile::whereHas('user', fn ($q) => $q->where('status', \App\Enums\AccountStatus::PENDING))->count())
                ->modifyQueryUsing(fn ($query) => $query->whereHas('user', fn ($q) => $q->where('status', \App\Enums\AccountStatus::PENDING))),
            'approved' => Tab::make(__('Approved'))
                ->icon('heroicon-o-check-circle')
                ->badge(StudentProfile::whereHas('user', fn ($q) => $q->where('status', \App\Enums\AccountStatus::APPROVED))->count())
                ->modifyQueryUsing(fn ($query) => $query->whereHas('user', fn ($q) => $q->where('status', \App\Enums\AccountStatus::APPROVED))),
        ];
    }
}
