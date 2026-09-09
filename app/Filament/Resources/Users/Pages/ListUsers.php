<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All Accounts'))
                ->badge(User::count()),
            'pending' => Tab::make(__('Pending Approval'))
                ->icon('heroicon-o-clock')
                ->badge(User::where('status', \App\Enums\AccountStatus::PENDING)->count())
                ->modifyQueryUsing(fn($query) => $query->where('status', \App\Enums\AccountStatus::PENDING)),
            'approved' => Tab::make(__('Approved'))
                ->icon('heroicon-o-check-circle')
                ->badge(User::where('status', \App\Enums\AccountStatus::APPROVED)->count())
                ->modifyQueryUsing(fn($query) => $query->where('status', \App\Enums\AccountStatus::APPROVED)),
            'students' => Tab::make(__('Students'))
                ->icon('heroicon-o-academic-cap')
                ->badge(User::roleStudent()->count())
                ->modifyQueryUsing(fn($query) => $query->roleStudent()),
            'teachers' => Tab::make(__('Teachers'))
                ->icon('heroicon-o-briefcase')
                ->badge(User::roleTeacher()->count())
                ->modifyQueryUsing(fn($query) => $query->roleTeacher()),
            'parents' => Tab::make(__('Parents'))
                ->icon('heroicon-o-user-group')
                ->badge(User::roleParent()->count())
                ->modifyQueryUsing(fn($query) => $query->roleParent()),
            'admins' => Tab::make(__('Admins'))
                ->icon('heroicon-o-shield-check')
                ->badge(User::roleAdmin()->count())
                ->modifyQueryUsing(fn($query) => $query->roleAdmin()),
        ];
    }
}
