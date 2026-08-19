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
            'all' => Tab::make('All Accounts (الجميع)')
                ->badge(User::count()),
            'pending' => Tab::make('⏳ Pending Approval (قيد المراجعة)')
                ->badge(User::where('status', \App\Enums\AccountStatus::PENDING)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', \App\Enums\AccountStatus::PENDING)),
            'approved' => Tab::make('✅ Approved (مقبول)')
                ->badge(User::where('status', \App\Enums\AccountStatus::APPROVED)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', \App\Enums\AccountStatus::APPROVED)),
            'students' => Tab::make('Students 🎓')
                ->badge(User::whereHas('studentProfile')->count())
                ->modifyQueryUsing(fn ($query) => $query->whereHas('studentProfile')),
            'teachers' => Tab::make('Teachers 👨‍🏫')
                ->badge(User::whereHas('teacherProfile')->count())
                ->modifyQueryUsing(fn ($query) => $query->whereHas('teacherProfile')),
            'parents' => Tab::make('Parents 👨‍👩‍👧')
                ->badge(User::whereHas('parentProfile')->count())
                ->modifyQueryUsing(fn ($query) => $query->whereHas('parentProfile')),
            'admins' => Tab::make('Admins ⚡')
                ->badge(User::whereHas('adminProfile')->count())
                ->modifyQueryUsing(fn ($query) => $query->whereHas('adminProfile')),
        ];
    }
}
