<?php

namespace App\Filament\Resources\CourseSessions\Pages;

use App\Filament\Resources\CourseSessions\CourseSessionResource;
use App\Models\CourseSession;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;

class ListCourseSessions extends ListRecords
{
    protected static string $resource = CourseSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Course Sessions (جميع الحصص المنهجية)')
                ->badge(CourseSession::count()),
            'free_demo' => Tab::make('🎓 Free Demo Sessions (الحصص المجانية)')
                ->badge(CourseSession::where('is_free_demo', true)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('is_free_demo', true)),
            'regular' => Tab::make('📚 Regular Sessions (الحصص الأساسية)')
                ->badge(CourseSession::where('is_free_demo', false)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('is_free_demo', false)),
        ];
    }
}
